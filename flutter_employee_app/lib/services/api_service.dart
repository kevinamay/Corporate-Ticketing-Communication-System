import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/api_constants.dart';
import '../models/department_model.dart';
import '../models/message_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';

class ApiService {
  static const String keyToken = 'auth_token';
  static const String keyUserData = 'auth_user_data';

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
  // Authentication
  // ---------------------------------------------------------
  static Future<Map<String, dynamic>> login(String loginId, String password) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final url = Uri.parse('$baseUrl/auth/login-employee');

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
          'message': data['message'] ?? 'Login gagal. Silakan periksa kredensial Anda.',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server: ${e.toString()}',
      };
    }
  }

  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String whatsappNumber,
    String? nationalIdKtp,
    required int departmentId,
    required String password,
    String gender = 'male',
    String? completeAddress,
  }) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final url = Uri.parse('$baseUrl/auth/register');

      final response = await http.post(
        url,
        headers: await _headers(withAuth: false),
        body: jsonEncode({
          'name': name,
          'email': email.trim().toLowerCase(),
          'whatsapp_number': whatsappNumber.trim(),
          'national_id_ktp': nationalIdKtp,
          'department_id': departmentId,
          'password': password,
          'gender': gender,
          'complete_address': completeAddress,
        }),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        return {'success': true, 'message': data['message']};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Pendaftaran gagal. Silakan periksa data Anda.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
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
  // Master Data & KPI
  // ---------------------------------------------------------
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

  static Future<Map<String, List<String>>> getCategories() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final response = await http.get(
        Uri.parse('$baseUrl/categories'),
        headers: await _headers(withAuth: false),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final Map<String, dynamic> raw = data['categories'] ?? {};
        return raw.map((k, v) => MapEntry(k, List<String>.from(v)));
      }
    } catch (_) {}
    return {};
  }

  static Future<Map<String, int>> getEmployeeKpi() async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final response = await http.get(
        Uri.parse('$baseUrl/employee/kpi'),
        headers: await _headers(withAuth: true),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final metrics = data['metrics'] as Map<String, dynamic>;
        return {
          'total_tickets': metrics['total_tickets'] ?? 0,
          'pending_tickets': metrics['pending_tickets'] ?? 0,
          'in_progress_tickets': metrics['in_progress_tickets'] ?? 0,
          'resolved_tickets': metrics['resolved_tickets'] ?? 0,
          'active_tickets': metrics['active_tickets'] ?? 0,
        };
      }
    } catch (_) {}
    return {
      'total_tickets': 0,
      'pending_tickets': 0,
      'in_progress_tickets': 0,
      'resolved_tickets': 0,
      'active_tickets': 0,
    };
  }

  // ---------------------------------------------------------
  // Employee Tickets
  // ---------------------------------------------------------
  static Future<List<TicketModel>> getEmployeeTickets({String? status, String? search}) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final queryParams = <String, String>{};
      if (status != null && status.isNotEmpty && status != 'Semua' && status != 'All') {
        queryParams['status'] = status;
      }
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      final uri = Uri.parse('$baseUrl/employee/tickets').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _headers(withAuth: true));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final list = data['tickets'] as List;
        return list.map((e) => TicketModel.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> createTicket({
    required String title,
    required int targetDepartmentId,
    required String category,
    required String priority,
    required String description,
    File? photoFile,
    String? photoBase64,
  }) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/employee/tickets');

      String? base64Payload = photoBase64;
      if (photoFile != null && base64Payload == null) {
        final bytes = await photoFile.readAsBytes();
        base64Payload = base64Encode(bytes);
      }

      final response = await http.post(
        uri,
        headers: await _headers(withAuth: true),
        body: jsonEncode({
          'title': title,
          'target_department_id': targetDepartmentId,
          'category': category,
          'priority': priority,
          'description': description,
          'photo_base64': base64Payload,
        }),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'ticket': TicketModel.fromJson(data['ticket']),
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal membuat tiket.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan pengiriman tiket: $e'};
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

  static Future<Map<String, dynamic>> sendMessage(int ticketId, String message, {File? photoFile, String? photoBase64}) async {
    try {
      final baseUrl = await ApiConstants.getBaseUrl();
      final uri = Uri.parse('$baseUrl/tickets/$ticketId/messages');

      String? base64Payload = photoBase64;
      if (photoFile != null && base64Payload == null) {
        final bytes = await photoFile.readAsBytes();
        base64Payload = base64Encode(bytes);
      }

      final response = await http.post(
        uri,
        headers: await _headers(withAuth: true),
        body: jsonEncode({
          'message': message,
          'photo_base64': base64Payload,
        }),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': MessageModel.fromJson(data['message']),
          'updated_ticket_status': data['updated_ticket_status'],
        };
      } else {
        return {
          'success': false,
          'message_text': data['message'] ?? 'Gagal mengirim pesan.',
        };
      }
    } catch (e) {
      return {'success': false, 'message_text': e.toString()};
    }
  }
}
