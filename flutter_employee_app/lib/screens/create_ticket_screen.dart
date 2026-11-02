import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../models/department_model.dart';
import '../services/api_service.dart';

class CreateTicketScreen extends StatefulWidget {
  final VoidCallback onTicketCreated;
  const CreateTicketScreen({super.key, required this.onTicketCreated});

  @override
  State<CreateTicketScreen> createState() => _CreateTicketScreenState();
}

class _CreateTicketScreenState extends State<CreateTicketScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descController = TextEditingController();

  List<DepartmentModel> _departments = [];
  Map<String, List<String>> _categoriesMap = {};
  int? _selectedDepartmentId;
  String? _selectedCategory;
  String _selectedPriority = 'Medium';

  File? _selectedImage;
  String? _base64Image;
  final ImagePicker _picker = ImagePicker();

  bool _isLoadingDepts = true;
  bool _isSubmitting = false;
  String? _errorMessage;

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
    final depts = await ApiService.getDepartments();
    final cats = await ApiService.getCategories();

    if (!mounted) return;

    setState(() {
      _departments = depts;
      _categoriesMap = cats;
      if (_departments.isNotEmpty) {
        _selectedDepartmentId = _departments.first.id;
        _updateCategoriesForDept(_departments.first.name);
      }
      _isLoadingDepts = false;
    });
  }

  void _updateCategoriesForDept(String deptName) {
    List<String> list = _categoriesMap[deptName] ?? [
      'Hardware & PC',
      'Software & OS',
      'Network & WiFi',
      'Printer & Scanner',
      'Fasilitas & Utilitas',
      'Lainnya',
    ];
    setState(() {
      _selectedCategory = list.isNotEmpty ? list.first : 'Lainnya';
    });
  }

  Future<void> _pickImage(ImageSource source) async {
    try {
      final picked = await _picker.pickImage(
        source: source,
        maxWidth: 1200,
        maxHeight: 1200,
        imageQuality: 80,
      );
      if (picked != null) {
        final file = File(picked.path);
        final bytes = await file.readAsBytes();
        setState(() {
          _selectedImage = file;
          _base64Image = base64Encode(bytes);
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
    if (_selectedDepartmentId == null || _selectedCategory == null) {
      setState(() => _errorMessage = 'Pilih divisi tujuan dan kategori');
      return;
    }

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    final res = await ApiService.createTicket(
      title: _titleController.text.trim(),
      targetDepartmentId: _selectedDepartmentId!,
      category: _selectedCategory!,
      priority: _selectedPriority,
      description: _descController.text.trim(),
      photoBase64: _base64Image,
    );

    if (!mounted) return;

    setState(() {
      _isSubmitting = false;
    });

    if (res['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Tiket berhasil dikirim!'),
          backgroundColor: const Color(0xFF10B981),
        ),
      );
      _titleController.clear();
      _descController.clear();
      setState(() {
        _selectedImage = null;
        _base64Image = null;
      });
      widget.onTicketCreated();
    } else {
      setState(() {
        _errorMessage = res['message'];
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text(
          'Buat Tiket Baru',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: false,
      ),
      body: SafeArea(
        child: _isLoadingDepts
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 500),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
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
                                style: const TextStyle(color: Color(0xFF991B1B), fontSize: 12, fontWeight: FontWeight.bold),
                              ),
                            ),
                            const SizedBox(height: 14),
                          ],

                          // Target Department
                          _label('Tujukan Ke Divisi / Departemen *'),
                          DropdownButtonFormField<int>(
                            value: _selectedDepartmentId,
                            decoration: _inputDecoration('Pilih Divisi Penanggung Jawab', Icons.business_rounded),
                            items: _departments.map((d) {
                              return DropdownMenuItem<int>(
                                value: d.id,
                                child: Text(d.name, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() {
                                _selectedDepartmentId = val;
                                final found = _departments.firstWhere((d) => d.id == val);
                                _updateCategoriesForDept(found.name);
                              });
                            },
                          ),
                          const SizedBox(height: 16),

                          // Category
                          _label('Kategori Kendala *'),
                          DropdownButtonFormField<String>(
                            value: _selectedCategory,
                            decoration: _inputDecoration('Pilih Kategori Permasalahan', Icons.category_outlined),
                            items: (_categoriesMap[_departments.firstWhere((d) => d.id == _selectedDepartmentId, orElse: () => _departments.first).name] ?? [
                              'Hardware & PC',
                              'Software & OS',
                              'Network & WiFi',
                              'Lainnya',
                            ]).map((cat) {
                              return DropdownMenuItem<String>(
                                value: cat,
                                child: Text(cat, style: const TextStyle(fontSize: 13)),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() => _selectedCategory = val);
                            },
                          ),
                          const SizedBox(height: 16),

                          // Priority
                          _label('Tingkat Prioritas *'),
                          Row(
                            children: ['Low', 'Medium', 'High', 'Urgent'].map((p) {
                              final isSelected = _selectedPriority == p;
                              Color chipColor = const Color(0xFF64748B);
                              if (p == 'Low') chipColor = const Color(0xFF10B981);
                              if (p == 'Medium') chipColor = const Color(0xFF3B82F6);
                              if (p == 'High') chipColor = const Color(0xFFF59E0B);
                              if (p == 'Urgent') chipColor = const Color(0xFFEF4444);

                              return Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 2),
                                  child: ChoiceChip(
                                    label: Text(
                                      p,
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.bold,
                                        color: isSelected ? Colors.white : chipColor,
                                      ),
                                    ),
                                    selected: isSelected,
                                    selectedColor: chipColor,
                                    backgroundColor: chipColor.withValues(alpha: 0.1),
                                    side: BorderSide(color: isSelected ? chipColor : chipColor.withValues(alpha: 0.3)),
                                    onSelected: (val) {
                                      if (val) setState(() => _selectedPriority = p);
                                    },
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                          const SizedBox(height: 16),

                          // Title
                          _label('Judul Kendala *'),
                          TextFormField(
                            controller: _titleController,
                            decoration: _inputDecoration('Contoh: Printer Inkjet Line 2 Tidak Menyala', Icons.title_rounded),
                            validator: (v) {
                              if (v == null || v.trim().isEmpty) return 'Judul wajib diisi';
                              if (v.trim().length < 4) return 'Judul minimal 4 karakter';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Description
                          _label('Deskripsi Lengkap Kendala *'),
                          TextFormField(
                            controller: _descController,
                            maxLines: 4,
                            decoration: _inputDecoration('Jelaskan detail kendala, lokasi mesin, atau langkah yang sudah dicoba...', Icons.description_outlined),
                            validator: (v) {
                              if (v == null || v.trim().isEmpty) return 'Deskripsi wajib diisi';
                              if (v.trim().length < 5) return 'Deskripsi minimal 5 karakter';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Photo Attachment
                          _label('Lampiran Bukti Foto (Opsional)'),
                          if (_selectedImage != null)
                            Stack(
                              children: [
                                Container(
                                  width: double.infinity,
                                  height: 180,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(color: const Color(0xFFCBD5E1)),
                                    image: DecorationImage(
                                      image: FileImage(_selectedImage!),
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                ),
                                Positioned(
                                  top: 8,
                                  right: 8,
                                  child: CircleAvatar(
                                    backgroundColor: Colors.black.withValues(alpha: 0.6),
                                    radius: 16,
                                    child: IconButton(
                                      icon: const Icon(Icons.close, size: 16, color: Colors.white),
                                      onPressed: () {
                                        setState(() {
                                          _selectedImage = null;
                                          _base64Image = null;
                                        });
                                      },
                                    ),
                                  ),
                                ),
                              ],
                            )
                          else
                            Row(
                              children: [
                                Expanded(
                                  child: OutlinedButton.icon(
                                    onPressed: () => _pickImage(ImageSource.camera),
                                    icon: const Icon(Icons.camera_alt_outlined, size: 18),
                                    label: const Text('Ambil Foto', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                    style: OutlinedButton.styleFrom(
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                      side: const BorderSide(color: Color(0xFFCBD5E1)),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: OutlinedButton.icon(
                                    onPressed: () => _pickImage(ImageSource.gallery),
                                    icon: const Icon(Icons.photo_library_outlined, size: 18),
                                    label: const Text('Dari Galeri', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                    style: OutlinedButton.styleFrom(
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                      side: const BorderSide(color: Color(0xFFCBD5E1)),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          const SizedBox(height: 28),

                          // Submit Button
                          ElevatedButton(
                            onPressed: _isSubmitting ? null : _submitTicket,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF2563EB),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 15),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                              elevation: 2,
                            ),
                            child: _isSubmitting
                                ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                : const Text('KIRIM PENGAJUAN TIKET', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, letterSpacing: 0.5)),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
      ),
    );
  }

  Widget _label(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Text(
        text,
        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF334155)),
      ),
    );
  }

  InputDecoration _inputDecoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
      prefixIcon: Icon(icon, size: 18, color: const Color(0xFF3B82F6)),
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF2563EB), width: 1.8)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
    );
  }
}
