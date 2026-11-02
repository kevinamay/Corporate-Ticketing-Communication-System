import 'package:shared_preferences/shared_preferences.dart';

class ApiConstants {
  static const String defaultBaseUrl = 'https://ticketing-kappa-jet.vercel.app/api';
  static const String keyBaseUrl = 'custom_base_url';

  static Future<String> getBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(keyBaseUrl) ?? defaultBaseUrl;
  }

  static Future<void> setBaseUrl(String url) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(keyBaseUrl, url.trim().replaceAll(RegExp(r'/+$'), ''));
  }
}
