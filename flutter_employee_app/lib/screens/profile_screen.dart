import 'package:flutter/material.dart';
import '../constants/api_constants.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
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
        title: const Text('Keluar dari Akun?'),
        content: const Text('Anda harus login kembali untuk mengakses pengajuan tiket.'),
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
        MaterialPageRoute(builder: (_) => const LoginScreen()),
        (route) => false,
      );
    }
  }

  void _showServerConfigDialog() {
    final controller = TextEditingController(text: _currentServerUrl);
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Konfigurasi Server API'),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(
            labelText: 'Base URL Server',
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
                  const SnackBar(content: Text('URL Server berhasil disimpan!')),
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
        title: const Text('Profil Karyawan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A))),
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
              // Profile Identity Card
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 4)),
                  ],
                ),
                child: Column(
                  children: [
                    CircleAvatar(
                      radius: 36,
                      backgroundColor: const Color(0xFF2563EB),
                      backgroundImage: _user?.avatar != null ? NetworkImage(_user!.avatar!) : null,
                      child: _user?.avatar == null
                          ? const Icon(Icons.person, color: Colors.white, size: 36)
                          : null,
                    ),
                    const SizedBox(height: 12),
                    Text(
                      _user?.name ?? 'Nama Karyawan',
                      style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _user?.email ?? '',
                      style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                    ),
                    const SizedBox(height: 10),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFF6FF),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFBFDBFE)),
                      ),
                      child: Text(
                        _user?.departmentName ?? 'Karyawan PT Asia Plastik',
                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Account Details List
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    _infoTile(Icons.phone_outlined, 'No. WhatsApp', _user?.whatsappNumber ?? '-'),
                    const Divider(height: 1, indent: 50),
                    _infoTile(Icons.credit_card_outlined, 'Nomor KTP (NIK)', _user?.nationalIdKtp ?? '-'),
                    const Divider(height: 1, indent: 50),
                    _infoTile(
                      Icons.verified_user_outlined,
                      'Status Verifikasi',
                      _user?.isApproved == true ? 'Aktif (Disetujui Admin)' : 'Menunggu Persetujuan',
                      valueColor: _user?.isApproved == true ? const Color(0xFF10B981) : const Color(0xFFF59E0B),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Settings & Network Config
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  children: [
                    ListTile(
                      leading: const Icon(Icons.dns_outlined, color: Color(0xFF2563EB)),
                      title: const Text('Koneksi Server API', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                      subtitle: Text(_currentServerUrl, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                      trailing: const Icon(Icons.chevron_right, size: 20),
                      onTap: _showServerConfigDialog,
                    ),
                    const Divider(height: 1, indent: 50),
                    const ListTile(
                      leading: Icon(Icons.info_outline, color: Color(0xFF64748B)),
                      title: Text('Versi Aplikasi Karyawan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                      subtitle: Text('v1.0.0 (Asia Plastik Flutter Client)', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Logout Button
              ElevatedButton.icon(
                onPressed: _confirmLogout,
                icon: const Icon(Icons.logout_rounded, size: 18),
                label: const Text('Keluar dari Akun (Logout)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
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
      leading: Icon(icon, color: const Color(0xFF2563EB), size: 22),
      title: Text(title, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
      subtitle: Text(
        value,
        style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: valueColor ?? const Color(0xFF0F172A)),
      ),
    );
  }
}
