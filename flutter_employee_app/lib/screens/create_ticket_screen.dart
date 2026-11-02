import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../models/department_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'login_screen.dart';

class CreateTicketScreen extends StatefulWidget {
  final VoidCallback onTicketCreated;

  const CreateTicketScreen({
    super.key,
    required this.onTicketCreated,
  });

  @override
  State<CreateTicketScreen> createState() => _CreateTicketScreenState();
}

class _CreateTicketScreenState extends State<CreateTicketScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descController = TextEditingController();

  UserModel? _user;
  List<DepartmentModel> _departments = [];
  Map<String, List<String>> _categoriesMap = {};
  int? _selectedSenderDeptId;
  int? _selectedTargetDeptId;
  String? _selectedCategory;

  final ImagePicker _picker = ImagePicker();
  Uint8List? _imageBytes;
  String? _base64Image;
  String? _imageFileName;

  bool _isLoading = true;
  bool _isSubmitting = false;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    _loadMetadata();
  }

  @override
  void dispose() {
    _titleController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _loadMetadata() async {
    final user = await ApiService.getSavedUser();
    final depts = await ApiService.getDepartments();
    final cats = await ApiService.getCategories();

    if (!mounted) return;

    setState(() {
      _user = user;
      _departments = depts;
      _categoriesMap = cats;

      if (_departments.isNotEmpty) {
        _selectedSenderDeptId = user?.departmentId ?? _departments.first.id;
        _selectedTargetDeptId = _departments.first.id;
        _updateCategoriesForDept(_departments.first.name);
      }
      _isLoading = false;
    });
  }

  void _updateCategoriesForDept(String deptName) {
    final list = _categoriesMap[deptName] ?? [
      'Network & WiFi',
      'Hardware & PC',
      'Software & OS',
      'Printer & Scanner',
      'Email & Akun',
      'Lainnya',
    ];
    setState(() {
      _selectedCategory = list.isNotEmpty ? list.first : 'Lainnya';
    });
  }

  void _beautifyDescription() {
    final current = _descController.text.trim();
    if (current.isEmpty) {
      _descController.text =
          "[Kendala]: Gangguan operasional pada sistem/perangkat kerja.\n"
          "[Lokasi / Aset]: Lantai kerja / No. Aset PC / Ruang operasional.\n"
          "[Dampak Operasional]: Proses input data pekerjaan terhambat.\n"
          "[Tindakan Mandiri]: Sudah mencoba restart perangkat dan memeriksa kabel koneksi.";
    } else {
      _descController.text =
          "[Kendala]: $current\n\n"
          "[Lokasi / Aset]: Area kerja operasional PT. Asia Plastik\n"
          "[Dampak]: Pekerjaan membutuhkan penanganan dari tim teknisi\n"
          "[Tindakan]: Telah dilakukan pengecekan awal secara mandiri";
    }
    setState(() {});
  }

  Future<void> _pickPhoto(ImageSource source) async {
    try {
      final picked = await _picker.pickImage(
        source: source,
        maxWidth: 1280,
        maxHeight: 1280,
        imageQuality: 82,
      );
      if (picked != null) {
        final bytes = await picked.readAsBytes();
        setState(() {
          _imageBytes = bytes;
          _base64Image = base64Encode(bytes);
          _imageFileName = picked.name;
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memilih gambar: $e')),
        );
      }
    }
  }

  Future<void> _submitTicket() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedTargetDeptId == null || _selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih departemen tujuan dan kategori tiket')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    final res = await ApiService.createTicket(
      title: _titleController.text.trim(),
      targetDepartmentId: _selectedTargetDeptId!,
      category: _selectedCategory!,
      priority: 'Medium',
      description: _descController.text.trim(),
      photoBase64: _base64Image,
    );

    if (!mounted) return;

    setState(() => _isSubmitting = false);

    if (res['success'] == true) {
      final ticket = res['ticket'] as TicketModel?;
      setState(() {
        _successMessage = 'Tiket Berhasil Diajukan #${ticket?.id ?? ''}';
        _titleController.clear();
        _descController.clear();
        _imageBytes = null;
        _base64Image = null;
        _imageFileName = null;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tiket berhasil dikirim ke antrean departemen tujuan!'),
          backgroundColor: Color(0xFF10B981),
        ),
      );

      // Trigger navigation / reload
      widget.onTicketCreated();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Gagal membuat tiket.'),
          backgroundColor: const Color(0xFFEF4444),
        ),
      );
    }
  }

  void _logout() async {
    await ApiService.logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginScreen()), (r) => false);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: Color(0xFFF8FAFC),
        body: Center(
          child: CircularProgressIndicator(color: Color(0xFF2563EB)),
        ),
      );
    }

    final senderDeptName = _departments.firstWhere(
      (d) => d.id == _selectedSenderDeptId,
      orElse: () => DepartmentModel(id: 0, name: _user?.departmentName ?? 'IT Support'),
    ).name;

    final targetDept = _departments.firstWhere(
      (d) => d.id == _selectedTargetDeptId,
      orElse: () => DepartmentModel(id: 0, name: 'IT Support'),
    );

    final availableCategories = _categoriesMap[targetDept.name] ?? [
      'Network & WiFi',
      'Hardware & PC',
      'Software & OS',
      'Printer & Scanner',
      'Email & Akun',
      'Lainnya',
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Top Corporate Navbar
              _buildCorporateNavbar(),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // ========================================================
                    // SUBMIT SUPPORT REQUEST CARD (GAMBAR 3)
                    // ========================================================
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.04),
                            blurRadius: 14,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Header Bar
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(7),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEFF6FF),
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(color: const Color(0xFFBFDBFE)),
                                  ),
                                  child: const Icon(Icons.description_outlined, color: Color(0xFF2563EB), size: 18),
                                ),
                                const SizedBox(width: 8),
                                const Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Submit Support Request',
                                        style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                                      ),
                                      Text(
                                        'Dispatch official ticket to designated corporate department',
                                        style: TextStyle(fontSize: 10, color: Color(0xFF64748B)),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 4),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEFF6FF),
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(color: const Color(0xFFBFDBFE)),
                                  ),
                                  child: const Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(Icons.circle, color: Color(0xFF2563EB), size: 5),
                                      SizedBox(width: 3),
                                      Text(
                                        'Live Routing',
                                        style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const Divider(height: 1, color: Color(0xFFF1F5F9)),

                          // Success Alert
                          if (_successMessage != null)
                            Container(
                              margin: const EdgeInsets.fromLTRB(14, 12, 14, 0),
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: const Color(0xFFECFDF5),
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: const Color(0xFFA7F3D0)),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.check_circle, color: Color(0xFF059669), size: 16),
                                  const SizedBox(width: 6),
                                  Expanded(
                                    child: Text(
                                      _successMessage!,
                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF065F46)),
                                    ),
                                  ),
                                  IconButton(
                                    icon: const Icon(Icons.close, size: 14, color: Color(0xFF065F46)),
                                    onPressed: () => setState(() => _successMessage = null),
                                  ),
                                ],
                              ),
                            ),

                          // Form Body
                          Padding(
                            padding: const EdgeInsets.all(14),
                            child: Form(
                              key: _formKey,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.stretch,
                                children: [
                                  _fieldLabel('JUDUL PERMINTAAN'),
                                  const SizedBox(height: 5),
                                  TextFormField(
                                    controller: _titleController,
                                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                                    decoration: _inputDecoration('Contoh: Gangguan koneksi VPN / Printer Offline di Lantai 2'),
                                    validator: (val) {
                                      if (val == null || val.trim().isEmpty) return 'Judul tiket wajib diisi';
                                      return null;
                                    },
                                  ),
                                  const SizedBox(height: 12),

                                  _fieldLabel('DEPARTEMEN PENGIRIM'),
                                  const SizedBox(height: 5),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFF8FAFC),
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(color: const Color(0xFFCBD5E1)),
                                    ),
                                    child: Row(
                                      children: [
                                        const Icon(Icons.business_outlined, size: 14, color: Color(0xFF64748B)),
                                        const SizedBox(width: 6),
                                        Expanded(
                                          child: Text(
                                            senderDeptName,
                                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(height: 12),

                                  _fieldLabel('DEPARTEMEN TUJUAN'),
                                  const SizedBox(height: 5),
                                  DropdownButtonFormField<int>(
                                    value: _selectedTargetDeptId,
                                    isDense: true,
                                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                                    decoration: _inputDecoration(''),
                                    items: _departments.map((d) {
                                      return DropdownMenuItem<int>(
                                        value: d.id,
                                        child: Text(d.name, style: const TextStyle(fontSize: 11, color: Color(0xFF0F172A))),
                                      );
                                    }).toList(),
                                    onChanged: (val) {
                                      if (val != null) {
                                        setState(() {
                                          _selectedTargetDeptId = val;
                                          final dept = _departments.firstWhere((d) => d.id == val);
                                          _updateCategoriesForDept(dept.name);
                                        });
                                      }
                                    },
                                  ),
                                  const SizedBox(height: 12),

                                  _fieldLabel('KATEGORI PERMINTAAN'),
                                  const SizedBox(height: 5),
                                  DropdownButtonFormField<String>(
                                    value: availableCategories.contains(_selectedCategory) ? _selectedCategory : availableCategories.first,
                                    isDense: true,
                                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                                    decoration: _inputDecoration(''),
                                    items: availableCategories.map((c) {
                                      return DropdownMenuItem<String>(
                                        value: c,
                                        child: Text(c, style: const TextStyle(fontSize: 11, color: Color(0xFF0F172A))),
                                      );
                                    }).toList(),
                                    onChanged: (val) => setState(() => _selectedCategory = val),
                                  ),
                                  const SizedBox(height: 12),

                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      _fieldLabel('RINCIAN MASALAH / DESKRIPSI'),
                                      TextButton.icon(
                                        onPressed: _beautifyDescription,
                                        icon: const Icon(Icons.auto_fix_high, size: 12, color: Color(0xFF2563EB)),
                                        label: const Text('✨ Rapikan Deskripsi', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF2563EB))),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 5),
                                  TextFormField(
                                    controller: _descController,
                                    minLines: 3,
                                    maxLines: 5,
                                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                                    onChanged: (_) => setState(() {}),
                                    decoration: _inputDecoration('Jelaskan detail kendala, nomor aset, pesan error...'),
                                    validator: (val) {
                                      if (val == null || val.trim().length < 10) return 'Deskripsi masalah minimal 10 karakter';
                                      return null;
                                    },
                                  ),
                                  const SizedBox(height: 4),
                                  Wrap(
                                    alignment: WrapAlignment.spaceBetween,
                                    crossAxisAlignment: WrapCrossAlignment.center,
                                    children: [
                                      Text('${_descController.text.length} karakter (Min. 10)', style: const TextStyle(fontSize: 9, color: Color(0xFF94A3B8))),
                                      const Text('Format: Teks / Template Otomatis', style: TextStyle(fontSize: 9, color: Color(0xFF94A3B8))),
                                    ],
                                  ),
                                  const SizedBox(height: 12),

                                  _fieldLabel('BUKTI FOTO / LAMPIRAN (OPSIONAL)'),
                                  const SizedBox(height: 5),
                                  InkWell(
                                    onTap: () {
                                      showModalBottomSheet(
                                        context: context,
                                        builder: (_) => SafeArea(
                                          child: Wrap(
                                            children: [
                                              ListTile(
                                                leading: const Icon(Icons.photo_library),
                                                title: const Text('Pilih dari Galeri'),
                                                onTap: () {
                                                  Navigator.pop(context);
                                                  _pickPhoto(ImageSource.gallery);
                                                },
                                              ),
                                              ListTile(
                                                leading: const Icon(Icons.camera_alt),
                                                title: const Text('Ambil dari Kamera'),
                                                onTap: () {
                                                  Navigator.pop(context);
                                                  _pickPhoto(ImageSource.camera);
                                                },
                                              ),
                                            ],
                                          ),
                                        ),
                                      );
                                    },
                                    borderRadius: BorderRadius.circular(10),
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 12),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFFF8FAFC),
                                        borderRadius: BorderRadius.circular(8),
                                        border: Border.all(color: const Color(0xFFCBD5E1)),
                                      ),
                                      child: _imageBytes == null
                                          ? const Column(
                                              children: [
                                                Icon(Icons.add_photo_alternate_outlined, color: Color(0xFF2563EB), size: 24),
                                                SizedBox(height: 4),
                                                Text('Pilih Bukti Foto atau Sentuh di Sini', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF2563EB))),
                                                Text('(tangkapan layar error, fisik alat, dsb. Maks 10MB)', style: TextStyle(fontSize: 9, color: Color(0xFF94A3B8))),
                                              ],
                                            )
                                          : Row(
                                              children: [
                                                ClipRRect(borderRadius: BorderRadius.circular(6), child: Image.memory(_imageBytes!, width: 44, height: 44, fit: BoxFit.cover)),
                                                const SizedBox(width: 8),
                                                Expanded(
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.start,
                                                    children: [
                                                      Text(_imageFileName ?? 'Foto Terlampir', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                                      const Text('Siap diunggah bersama tiket', style: TextStyle(fontSize: 9, color: Color(0xFF10B981))),
                                                    ],
                                                  ),
                                                ),
                                                IconButton(
                                                  icon: const Icon(Icons.delete_outline, color: Color(0xFFEF4444), size: 18),
                                                  onPressed: () => setState(() {
                                                    _imageBytes = null;
                                                    _base64Image = null;
                                                    _imageFileName = null;
                                                  }),
                                                ),
                                              ],
                                            ),
                                    ),
                                  ),
                                  const SizedBox(height: 16),

                                  ElevatedButton.icon(
                                    onPressed: _isSubmitting ? null : _submitTicket,
                                    icon: _isSubmitting
                                        ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                        : const Icon(Icons.send_rounded, size: 14),
                                    label: Text(_isSubmitting ? 'Mengirim...' : 'Kirim Permintaan Tiket', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 12)),
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: const Color(0xFF2563EB),
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 80),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCorporateNavbar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: const BoxDecoration(
        color: Color(0xFF0F172A),
        border: Border(bottom: BorderSide(color: Color(0xFF1E293B))),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Image.asset(
            'assets/images/logo2.webp',
            height: 26,
            fit: BoxFit.contain,
            errorBuilder: (_, __, ___) => const Icon(Icons.business, color: Colors.white, size: 22),
          ),

          IconButton(
            icon: const Icon(Icons.logout_rounded, color: Color(0xFF94A3B8), size: 20),
            padding: const EdgeInsets.all(4),
            constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
            tooltip: 'Keluar',
            onPressed: _logout,
          ),
        ],
      ),
    );
  }

  Widget _fieldLabel(String text) {
    return Text(text, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: Color(0xFF334155), letterSpacing: 0.5));
  }

  InputDecoration _inputDecoration(String hint) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      isDense: true,
      filled: true,
      fillColor: Colors.white,
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFF2563EB), width: 1.5)),
      errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFFEF4444))),
      focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFFEF4444), width: 1.5)),
    );
  }
}
