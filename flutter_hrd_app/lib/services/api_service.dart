import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/api_constants.dart';
import '../models/department_model.dart';
import '../models/employee_model.dart';
import '../models/message_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';

class ApiService {
  static const String keyToken = 'hrd_auth_token';
  static const String keyUserData = 'hrd_user_data';

  // ---------------------------------------------------------
  // Session & Auth Helpers
  // ---------------------------------------------------------
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(keyToken);
  }

  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(keyToken, token);
  }

  static Future<UserModel?> getSavedUser() async {
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString(keyUserData);
    if (raw == null) return null;
    try {
      return UserModel.fromJson(jsonDecode(raw));
    } catch (_) {
      return null;
    }
  }

  static Future<void> saveUser(UserModel user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(keyUserData, jsonEncode(user.toJson()));
  }

  static Future<void> clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(keyToken);
    await prefs.remove(keyUserData);
  }

  static Future<Map<String, String>> _headers({bool withAuth = true}) async {
    final headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    if (withAuth) {
      final token = await getToken();
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }
    return headers;
  }

  // ---------------------------------------------------------
  // HRD Authentication
  // ---------------------------------------------------------
  static Future<Map<String, dynamic>> loginHrd(String loginId, String password) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final url = Uri.parse('$baseUrl/auth/login-hrd');

      final response = await http.post(
        url,
        headers: await _headers(withAuth: false),
        body: jsonEncode({
          'login_id': loginId.trim(),
          'password': password,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final token = data['token'];
        final user = UserModel.fromJson(data['user']);
        await saveToken(token);
        await saveUser(user);
        return {'success': true, 'message': data['message'], 'user': user};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Login HRD gagal. Silakan periksa kredensial Anda.',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server: ${e.toString()}',
      };
    }
  }

  static Future<void> logout() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final url = Uri.parse('$baseUrl/auth/logout');
      await http.post(url, headers: await _headers(withAuth: true));
    } catch (_) {}
    await clearSession();
  }

  // ---------------------------------------------------------
  // HRD Dashboard Metrics & Chart
  // ---------------------------------------------------------
  static Future<Map<String, dynamic>> getHrdMetrics() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final response = await http.get(
        Uri.parse('$baseUrl/hrd/metrics'),
        headers: await _headers(withAuth: true),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['metrics'] as Map<String, dynamic>;
      }
    } catch (_) {}
    return {
      'total_employees': 0,
      'pending_approvals': 0,
      'total_tickets': 0,
      'pending_tickets': 0,
      'in_progress_tickets': 0,
      'resolved_tickets': 0,
      'resolution_rate': 0.0,
      'departments': [],
    };
  }

  static Future<List<Map<String, dynamic>>> getDailyChart() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final response = await http.get(
        Uri.parse('$baseUrl/hrd/daily-chart'),
        headers: await _headers(withAuth: true),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final list = data['trend'] as List;
        return list.map((e) => e as Map<String, dynamic>).toList();
      }
    } catch (_) {}
    return [];
  }

  // ---------------------------------------------------------
  // Global Tickets Management
  // ---------------------------------------------------------
  static Future<List<TicketModel>> getGlobalTickets({int? departmentId, String? status, String? search}) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final queryParams = <String, String>{};
      if (departmentId != null) {
        queryParams['department_id'] = departmentId.toString();
      }
      if (status != null && status.isNotEmpty && status != 'Semua' && status != 'All') {
        queryParams['status'] = status;
      }
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      final uri = Uri.parse('$baseUrl/hrd/tickets').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _headers(withAuth: true));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final list = data['tickets'] as List;
        return list.map((e) => TicketModel.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> updateTicketStatus(int id, String status, {String? note}) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/hrd/tickets/$id/status');

      final response = await http.patch(
        uri,
        headers: await _headers(withAuth: true),
        body: jsonEncode({
          'status': status,
          'note': note,
        }),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'ticket': TicketModel.fromJson(data['ticket']),
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memperbarui status.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<Map<String, dynamic>> getTicketDetail(int id) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/tickets/$id');
      final response = await http.get(uri, headers: await _headers(withAuth: true));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final ticket = TicketModel.fromJson(data['ticket']);
        final msgList = (data['messages'] as List).map((m) => MessageModel.fromJson(m)).toList();
        return {
          'success': true,
          'ticket': ticket,
          'messages': msgList,
        };
      }
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
    return {'success': false, 'message': 'Tiket tidak ditemukan.'};
  }

  static Future<Map<String, dynamic>> sendMessage(int ticketId, String message) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/tickets/$ticketId/messages');

      final response = await http.post(
        uri,
        headers: await _headers(withAuth: true),
        body: jsonEncode({'message': message}),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': MessageModel.fromJson(data['message']),
          'updated_ticket_status': data['updated_ticket_status'],
        };
      }
    } catch (_) {}
    return {'success': false, 'message_text': 'Gagal mengirim pesan respon.'};
  }

  // ---------------------------------------------------------
  // HCM Employee Master Data
  // ---------------------------------------------------------
  static Future<List<EmployeeModel>> getEmployees({String? search, String? approvalStatus}) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final queryParams = <String, String>{};
      if (search != null && search.isNotEmpty) queryParams['search'] = search;
      if (approvalStatus != null && approvalStatus.isNotEmpty && approvalStatus != 'all') {
        queryParams['approval_status'] = approvalStatus;
      }

      final uri = Uri.parse('$baseUrl/hrd/employees').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _headers(withAuth: true));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final list = data['employees'] as List;
        return list.map((e) => EmployeeModel.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> toggleEmployeeApproval(int id) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/hrd/employees/$id/toggle-approval');

      final response = await http.post(uri, headers: await _headers(withAuth: true));
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'is_approved': data['is_approved'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah status persetujuan.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<List<DepartmentModel>> getDepartments() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final response = await http.get(
        Uri.parse('$baseUrl/departments'),
        headers: await _headers(withAuth: false),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final list = data['departments'] as List;
        return list.map((e) => DepartmentModel.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }
}
