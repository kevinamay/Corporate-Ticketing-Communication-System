import 'package:flutter/material.dart';
import '../constants/api_constants.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'login_hrd_screen.dart';

class HrdProfileScreen extends StatefulWidget {
  const HrdProfileScreen({super.key});

  @override
  State<HrdProfileScreen> createState() => _HrdProfileScreenState();
}

class _HrdProfileScreenState extends State<HrdProfileScreen> {
  UserModel? _user;
  String _currentServerUrl = '';

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    final user = await ApiService.getSavedUser();
    final url = await ApiConstants.getBaseUrl();
    if (!mounted) return;
    setState(() {
      _user = user;
      _currentServerUrl = url;
    });
  }

  Future<void> _confirmLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Keluar dari Portal HRD?'),
        content: const Text('Sesi otentikasi HRD/Administrator Anda akan diakhiri.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFDC2626), foregroundColor: Colors.white),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await ApiService.logout();
      if (!mounted) return;
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const LoginHrdScreen()),
        (route) => false,
      );
    }
  }

  void _showServerConfigDialog() {
    final controller = TextEditingController(text: _currentServerUrl);
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Konfigurasi Endpoint API HRD'),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(
            labelText: 'Base URL API Laravel',
            hintText: 'https://ticketing-kappa-jet.vercel.app/api',
            border: OutlineInputBorder(),
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () async {
              await ApiConstants.setBaseUrl(controller.text);
              if (ctx.mounted) Navigator.pop(ctx);
              _loadProfile();
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Endpoint API berhasil diperbarui!')),
                );
              }
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Profil Administrator / HRD', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A))),
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: false,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(18),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // HRD Staff Profile Card
              Container(
                padding: const EdgeInsets.all(22),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF312E81), Color(0xFF4F46E5)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(22),
                  boxShadow: [
                    BoxShadow(color: const Color(0xFF4F46E5).withValues(alpha: 0.25), blurRadius: 14, offset: const Offset(0, 6)),
                  ],
                ),
                child: Column(
                  children: [
                    CircleAvatar(
                      radius: 38,
                      backgroundColor: Colors.white.withValues(alpha: 0.2),
                      backgroundImage: _user?.avatar != null ? NetworkImage(_user!.avatar!) : null,
                      child: _user?.avatar == null
                          ? const Icon(Icons.shield_rounded, color: Colors.white, size: 40)
                          : null,
                    ),
                    const SizedBox(height: 12),
                    Text(
                      _user?.name ?? 'Nama Staff HRD',
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.white),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _user?.email ?? '',
                      style: const TextStyle(fontSize: 12, color: Color(0xFFC7D2FE)),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.18),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.verified, color: Colors.amber, size: 14),
                          const SizedBox(width: 6),
                          Text(
                            (_user?.role ?? 'hrd').toUpperCase(),
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 0.5),
                          ),
                          const Text(' • ', style: TextStyle(color: Colors.white70)),
                          Text(
                            _user?.departmentName ?? 'Departemen HRD',
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),

              // Staff Metadata
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    _infoTile(Icons.phone_outlined, 'No. WhatsApp HRD', _user?.whatsappNumber ?? '-'),
                    const Divider(height: 1, indent: 50),
                    _infoTile(Icons.domain_outlined, 'Unit Operasional', _user?.departmentName ?? 'Kantor Pusat - HRD'),
                    const Divider(height: 1, indent: 50),
                    _infoTile(
                      Icons.security_outlined,
                      'Hak Akses Sistem',
                      'Full Access (Monitoring, ACC Karyawan & Tiket)',
                      valueColor: const Color(0xFF4F46E5),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Server and Application Info
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    ListTile(
                      leading: const Icon(Icons.dns_outlined, color: Color(0xFF4F46E5)),
                      title: const Text('Endpoint Backend Laravel', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                      subtitle: Text(_currentServerUrl, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                      trailing: const Icon(Icons.chevron_right, size: 20),
                      onTap: _showServerConfigDialog,
                    ),
                    const Divider(height: 1, indent: 50),
                    const ListTile(
                      leading: Icon(Icons.admin_panel_settings_outlined, color: Color(0xFF64748B)),
                      title: Text('Versi Aplikasi Mobile HRD', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                      subtitle: Text('v1.0.0 (Enterprise HRD Mobile Suite)', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Logout Button
              ElevatedButton.icon(
                onPressed: _confirmLogout,
                icon: const Icon(Icons.logout_rounded, size: 18),
                label: const Text('Keluar dari Akun HRD', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFEF2F2),
                  foregroundColor: const Color(0xFFDC2626),
                  elevation: 0,
                  side: const BorderSide(color: Color(0xFFFECACA)),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _infoTile(IconData icon, String title, String value, {Color? valueColor}) {
    return ListTile(
      leading: Icon(icon, color: const Color(0xFF4F46E5), size: 22),
      title: Text(title, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
      subtitle: Text(
        value,
        style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: valueColor ?? const Color(0xFF0F172A)),
      ),
    );
  }
}
