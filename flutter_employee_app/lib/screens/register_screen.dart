import 'package:flutter/material.dart';
import '../models/department_model.dart';
import '../services/api_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _whatsappController = TextEditingController();
  final _ktpController = TextEditingController();
  final _passwordController = TextEditingController();
  final _addressController = TextEditingController();

  int? _selectedDepartmentId;
  String _gender = 'male';
  bool _isLoading = false;
  bool _isLoadingDepartments = true;
  List<DepartmentModel> _departments = [];
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadDepartments();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _whatsappController.dispose();
    _ktpController.dispose();
    _passwordController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _loadDepartments() async {
    final depts = await ApiService.getDepartments();
    if (!mounted) return;
    setState(() {
      _departments = depts;
      if (_departments.isNotEmpty) {
        _selectedDepartmentId = _departments.first.id;
      }
      _isLoadingDepartments = false;
    });
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedDepartmentId == null) {
      setState(() => _errorMessage = 'Pilih divisi/departemen Anda');
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final res = await ApiService.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      whatsappNumber: _whatsappController.text.trim(),
      nationalIdKtp: _ktpController.text.trim(),
      departmentId: _selectedDepartmentId!,
      password: _passwordController.text,
      gender: _gender,
      completeAddress: _addressController.text.trim(),
    );

    if (!mounted) return;

    setState(() {
      _isLoading = false;
    });

    if (res['success'] == true) {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: const Row(
            children: [
              Icon(Icons.check_circle_rounded, color: Color(0xFF10B981), size: 28),
              SizedBox(width: 10),
              Text('Pendaftaran Berhasil', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            ],
          ),
          content: Text(
            res['message'] ?? 'Akun Anda berhasil didaftarkan dan menunggu verifikasi dari Admin.',
            style: const TextStyle(fontSize: 13, color: Color(0xFF334155)),
          ),
          actions: [
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF2563EB),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onPressed: () {
                Navigator.pop(ctx);
                Navigator.pop(context);
              },
              child: const Text('Ke Halaman Login'),
            ),
          ],
        ),
      );
    } else {
      setState(() {
        _errorMessage = res['message'];
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F172A),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          'Daftar Karyawan Baru',
          style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          child: Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 440),
              child: Container(
                padding: const EdgeInsets.all(22),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.25),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      const Text(
                        'Registrasi Identitas Karyawan',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFF0F172A),
                        ),
                      ),
                      const SizedBox(height: 4),
                      const Text(
                        'Lengkapi data untuk diverifikasi oleh Human Resources / Admin IT.',
                        style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                      ),
                      const SizedBox(height: 18),

                      if (_errorMessage != null) ...[
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: const Color(0xFFFEF2F2),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFFCA5A5)),
                          ),
                          child: Text(
                            _errorMessage!,
                            style: const TextStyle(color: Color(0xFF991B1B), fontSize: 12, fontWeight: FontWeight.w600),
                          ),
                        ),
                        const SizedBox(height: 14),
                      ],

                      // Nama Lengkap
                      TextFormField(
                        controller: _nameController,
                        decoration: _inputDecoration('Nama Lengkap Sesuai KTP', Icons.badge_outlined),
                        validator: (v) => (v == null || v.trim().isEmpty) ? 'Nama wajib diisi' : null,
                      ),
                      const SizedBox(height: 14),

                      // Email Perusahaan
                      TextFormField(
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        decoration: _inputDecoration('Email Perusahaan / Pribadi', Icons.email_outlined),
                        validator: (v) {
                          if (v == null || v.trim().isEmpty) return 'Email wajib diisi';
                          if (!v.contains('@')) return 'Format email tidak valid';
                          return null;
                        },
                      ),
                      const SizedBox(height: 14),

                      // WhatsApp
                      TextFormField(
                        controller: _whatsappController,
                        keyboardType: TextInputType.phone,
                        decoration: _inputDecoration('Nomor WhatsApp Aktif', Icons.phone_android_outlined),
                        validator: (v) => (v == null || v.trim().isEmpty) ? 'Nomor WhatsApp wajib diisi' : null,
                      ),
                      const SizedBox(height: 14),

                      // NIK KTP
                      TextFormField(
                        controller: _ktpController,
                        keyboardType: TextInputType.number,
                        decoration: _inputDecoration('Nomor KTP / NIK (Opsional)', Icons.credit_card_outlined),
                      ),
                      const SizedBox(height: 14),

                      // Departemen Dropdown
                      if (_isLoadingDepartments)
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 8),
                          child: Center(child: CircularProgressIndicator()),
                        )
                      else
                        DropdownButtonFormField<int>(
                          value: _selectedDepartmentId,
                          decoration: _inputDecoration('Pilih Divisi / Departemen', Icons.apartment_outlined),
                          items: _departments.map((d) {
                            return DropdownMenuItem<int>(
                              value: d.id,
                              child: Text(d.name, style: const TextStyle(fontSize: 13)),
                            );
                          }).toList(),
                          onChanged: (val) {
                            setState(() => _selectedDepartmentId = val);
                          },
                          validator: (v) => v == null ? 'Pilih divisi Anda' : null,
                        ),
                      const SizedBox(height: 14),

                      // Jenis Kelamin
                      Row(
                        children: [
                          const Text('Gender: ', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF475569))),
                          const SizedBox(width: 8),
                          ChoiceChip(
                            label: const Text('Laki-laki', style: TextStyle(fontSize: 12)),
                            selected: _gender == 'male',
                            onSelected: (s) => setState(() => _gender = 'male'),
                          ),
                          const SizedBox(width: 8),
                          ChoiceChip(
                            label: const Text('Perempuan', style: TextStyle(fontSize: 12)),
                            selected: _gender == 'female',
                            onSelected: (s) => setState(() => _gender = 'female'),
                          ),
                        ],
                      ),
                      const SizedBox(height: 14),

                      // Password
                      TextFormField(
                        controller: _passwordController,
                        obscureText: true,
                        decoration: _inputDecoration('Password Baru', Icons.lock_outline_rounded),
                        validator: (v) {
                          if (v == null || v.isEmpty) return 'Password wajib diisi';
                          if (v.length < 6) return 'Password minimal 6 karakter';
                          return null;
                        },
                      ),
                      const SizedBox(height: 14),

                      // Alamat Lengkap
                      TextFormField(
                        controller: _addressController,
                        maxLines: 2,
                        decoration: _inputDecoration('Alamat Tempat Tinggal (Opsional)', Icons.home_outlined),
                      ),
                      const SizedBox(height: 22),

                      // Submit Button
                      ElevatedButton(
                        onPressed: _isLoading ? null : _handleRegister,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF2563EB),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                        child: _isLoading
                            ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text('KIRIM PENDAFTARAN', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, letterSpacing: 0.5)),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  InputDecoration _inputDecoration(String label, IconData icon) {
    return InputDecoration(
      labelText: label,
      labelStyle: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
      prefixIcon: Icon(icon, size: 20, color: const Color(0xFF3B82F6)),
      filled: true,
      fillColor: const Color(0xFFF8FAFC),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF2563EB), width: 1.8)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
    );
  }
}
