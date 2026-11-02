import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import '../models/department_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'ticket_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  final Function(int)? onNavigateToTab;
  const HomeScreen({super.key, this.onNavigateToTab});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  // Prototype Variant Index: 0 = Variant A, 1 = Variant B, 2 = Variant C
  int _currentVariant = 0;
  final List<String> _variantNames = [
    'Varian A: Feed Korporat (Web Parity)',
    'Varian B: Executive Hub (Segmented Tabs)',
    'Varian C: Modern Bento Dashboard',
  ];

  // For Variant B Tab Controller
  int _variantBSelectedTab = 0;

  // Scroll Controller & Section Keys for In-Page Navigation
  final ScrollController _scrollController = ScrollController();
  final GlobalKey _formSectionKey = GlobalKey();
  final GlobalKey _queueSectionKey = GlobalKey();
  final GlobalKey _resolvedSectionKey = GlobalKey();

  // User & KPI
  UserModel? _user;
  Map<String, int> _kpi = {
    'total_tickets': 0,
    'pending_tickets': 0,
    'in_progress_tickets': 0,
    'resolved_tickets': 0,
    'active_tickets': 0,
  };

  // Departments & Categories
  List<DepartmentModel> _departments = [];
  Map<String, List<String>> _categoriesMap = {};
  int? _selectedSenderDeptId;
  int? _selectedTargetDeptId;
  String? _selectedCategory;

  // New Ticket Form State
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _descController = TextEditingController();
  final ImagePicker _picker = ImagePicker();
  Uint8List? _imageBytes;
  String? _base64Image;
  String? _imageFileName;
  bool _isSubmittingTicket = false;
  String? _ticketFormSuccessMsg;

  // Active Queue State
  List<TicketModel> _allTickets = [];
  String _activeQueueSearch = '';
  String _activeQueueStatus = 'Semua';
  int? _activeQueueDeptFilter;

  // Resolved Archive State
  String _resolvedSearch = '';
  int? _resolvedDeptFilter;

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadAllData();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _titleController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _loadAllData() async {
    final user = await ApiService.getSavedUser();
    final depts = await ApiService.getDepartments();
    final cats = await ApiService.getCategories();
    final kpi = await ApiService.getEmployeeKpi();
    final tickets = await ApiService.getEmployeeTickets();

    if (!mounted) return;

    setState(() {
      _user = user;
      _departments = depts;
      _categoriesMap = cats;
      _kpi = kpi;
      _allTickets = tickets;

      // Set default departments
      if (_departments.isNotEmpty) {
        _selectedSenderDeptId ??= user?.departmentId ?? _departments.first.id;
        _selectedTargetDeptId ??= _departments.first.id;
        _updateCategoriesForDept(_departments.first.name);
      }

      _isLoading = false;
    });
  }

  void _updateCategoriesForDept(String deptName) {
    final list = _categoriesMap[deptName] ?? [
      'Jaringan & Internet',
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

  void _scrollToKey(GlobalKey key) {
    final context = key.currentContext;
    if (context != null) {
      Scrollable.ensureVisible(
        context,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeInOutCubic,
      );
    }
  }

  // --------------------------------------------------------------------------
  // AI Beautifier / Rapid Description Formatter
  // --------------------------------------------------------------------------
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

  // --------------------------------------------------------------------------
  // Image Picking
  // --------------------------------------------------------------------------
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

  // --------------------------------------------------------------------------
  // Submit Ticket Request
  // --------------------------------------------------------------------------
  Future<void> _submitTicket() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedTargetDeptId == null || _selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih departemen tujuan dan kategori tiket')),
      );
      return;
    }

    setState(() => _isSubmittingTicket = true);

    final res = await ApiService.createTicket(
      title: _titleController.text.trim(),
      targetDepartmentId: _selectedTargetDeptId!,
      category: _selectedCategory!,
      priority: 'Medium',
      description: _descController.text.trim(),
      photoBase64: _base64Image,
    );

    if (!mounted) return;

    setState(() => _isSubmittingTicket = false);

    if (res['success'] == true) {
      final ticket = res['ticket'] as TicketModel?;
      setState(() {
        _ticketFormSuccessMsg = 'Tiket Berhasil Diajukan #${ticket?.id ?? ''}';
        _titleController.clear();
        _descController.clear();
        _imageBytes = null;
        _base64Image = null;
        _imageFileName = null;
      });

      _loadAllData();

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tiket berhasil diajukan dan masuk ke antrean!'),
          backgroundColor: Color(0xFF10B981),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Gagal membuat tiket.'),
          backgroundColor: const Color(0xFFEF4444),
        ),
      );
    }
  }

  // --------------------------------------------------------------------------
  // Quick Dialogs (AI & WhatsApp)
  // --------------------------------------------------------------------------
  void _openAiAssistantDialog() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Row(
          children: [
            Icon(Icons.auto_awesome, color: Color(0xFF2563EB), size: 22),
            SizedBox(width: 8),
            Text('Asisten AI Asia Plastik', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          ],
        ),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Halo! Saya asisten cerdas sistem internal. Ada kendala teknis yang ingin Anda laporkan?',
              style: TextStyle(fontSize: 13, height: 1.5),
            ),
            SizedBox(height: 12),
            Text(
              'Tips Pelaporan Cepat:\n'
              '• Sebutkan nama software / perangkat yang bermasalah\n'
              '• Sertakan pesan error atau foto tangkapan layar\n'
              '• Gunakan tombol "Rapikan Deskripsi" untuk format rapi otomatis',
              style: TextStyle(fontSize: 12, color: Color(0xFF64748B), height: 1.4),
            ),
          ],
        ),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF2563EB), foregroundColor: Colors.white),
            child: const Text('Siap, Paham!'),
          ),
        ],
      ),
    );
  }

  void _openWhatsAppSupport() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Row(
          children: [
            Icon(Icons.chat_bubble_outline, color: Color(0xFF10B981)),
            SizedBox(width: 8),
            Text('Bantuan Cepat WhatsApp', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          ],
        ),
        content: const Text(
          'Nomor Layanan Darurat IT / HRD Support PT. Asia Plastik:\n\n'
          '📱 Hotline IT Support: +62 857-8469-4910\n'
          '📱 Hotline HRD & GA: +62 812-3456-7800\n\n'
          'Jam operasional pabrik: 07.00 - 17.00 WIB',
          style: TextStyle(fontSize: 13, height: 1.5),
        ),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981), foregroundColor: Colors.white),
            child: const Text('Tutup'),
          ),
        ],
      ),
    );
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

    final filteredActiveTickets = _allTickets.where((t) {
      final matchesSearch = _activeQueueSearch.isEmpty ||
          t.title.toLowerCase().contains(_activeQueueSearch.toLowerCase()) ||
          t.description.toLowerCase().contains(_activeQueueSearch.toLowerCase());
      final matchesStatus = _activeQueueStatus == 'Semua' ||
          t.status.toLowerCase() == _activeQueueStatus.toLowerCase();
      final matchesDept = _activeQueueDeptFilter == null || t.targetDepartmentId == _activeQueueDeptFilter;
      return matchesSearch && matchesStatus && matchesDept;
    }).toList();

    final resolvedTickets = _allTickets.where((t) {
      final isResolved = t.status.toLowerCase() == 'resolved' || t.status.toLowerCase() == 'closed';
      final matchesSearch = _resolvedSearch.isEmpty ||
          t.title.toLowerCase().contains(_resolvedSearch.toLowerCase()) ||
          t.description.toLowerCase().contains(_resolvedSearch.toLowerCase());
      final matchesDept = _resolvedDeptFilter == null || t.targetDepartmentId == _resolvedDeptFilter;
      return isResolved && matchesSearch && matchesDept;
    }).toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      body: Stack(
        children: [
          SafeArea(
            child: RefreshIndicator(
              onRefresh: _loadAllData,
              color: const Color(0xFF2563EB),
              child: _renderCurrentVariant(filteredActiveTickets, resolvedTickets),
            ),
          ),

          // ============================================================
          // FLOATING PROTOTYPE SWITCHER (Bottom Pill Bar)
          // ============================================================
          Positioned(
            bottom: 16,
            left: 16,
            right: 16,
            child: Center(
              child: _buildPrototypeSwitcher(),
            ),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // PROTOTYPE SWITCHER BAR
  // ==========================================================================
  Widget _buildPrototypeSwitcher() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A).withValues(alpha: 0.94),
        borderRadius: BorderRadius.circular(30),
        border: Border.all(color: const Color(0xFF3B82F6).withValues(alpha: 0.6), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.35),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Left Arrow
          IconButton(
            icon: const Icon(Icons.arrow_back_ios_rounded, color: Colors.white, size: 14),
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 28, minHeight: 28),
            onPressed: () {
              setState(() {
                _currentVariant = (_currentVariant - 1 + _variantNames.length) % _variantNames.length;
              });
            },
          ),
          const SizedBox(width: 4),

          // Current Variant Badge & Name
          InkWell(
            onTap: () {
              setState(() {
                _currentVariant = (_currentVariant + 1) % _variantNames.length;
              });
            },
            borderRadius: BorderRadius.circular(20),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFF2563EB),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      'DESAIN ${_currentVariant + 1}/3',
                      style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900),
                    ),
                  ),
                  const SizedBox(width: 8),
                  ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 160),
                    child: Text(
                      _variantNames[_currentVariant],
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(width: 4),

          // Right Arrow
          IconButton(
            icon: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 14),
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 28, minHeight: 28),
            onPressed: () {
              setState(() {
                _currentVariant = (_currentVariant + 1) % _variantNames.length;
              });
            },
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // VARIANT ROUTER
  // ==========================================================================
  Widget _renderCurrentVariant(List<TicketModel> activeTickets, List<TicketModel> resolvedTickets) {
    switch (_currentVariant) {
      case 0:
        return _buildVariantA(activeTickets, resolvedTickets);
      case 1:
        return _buildVariantB(activeTickets, resolvedTickets);
      case 2:
      default:
        return _buildVariantC(activeTickets, resolvedTickets);
    }
  }

  // ==========================================================================
  // VARIAN A: FEED KORPORAT PRESISI (Web Parity Elegan & Responsif)
  // ==========================================================================
  Widget _buildVariantA(List<TicketModel> activeTickets, List<TicketModel> resolvedTickets) {
    return SingleChildScrollView(
      controller: _scrollController,
      physics: const AlwaysScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // 1. Sleek Mobile Corporate Top Bar (No crowded desktop buttons)
          _buildVariantANavbar(),

          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 1200),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // 2. Banner with scroll actions
                    _buildVariantABanner(resolvedTickets.length),
                    const SizedBox(height: 16),

                    // 3. Realtime KPI Metrics Strip
                    _buildKpiMetricsStrip(),
                    const SizedBox(height: 20),

                    // 4. Submit Support Request Card
                    Container(
                      key: _formSectionKey,
                      child: _buildSubmitTicketFormCard(),
                    ),
                    const SizedBox(height: 24),

                    // 5. Antrean Tiket & Insiden Aktif Card
                    Container(
                      key: _queueSectionKey,
                      child: _buildActiveQueueCard(activeTickets),
                    ),
                    const SizedBox(height: 24),

                    // 6. Riwayat Tiket Selesai Card
                    Container(
                      key: _resolvedSectionKey,
                      child: _buildResolvedArchiveCard(resolvedTickets),
                    ),
                    const SizedBox(height: 36),

                    // 7. Corporate Footer
                    _buildCorporateFooter(),
                    const SizedBox(height: 80),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVariantANavbar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: const BoxDecoration(
        color: Color(0xFF0F172A),
        border: Border(bottom: BorderSide(color: Color(0xFF1E293B))),
      ),
      child: Row(
        children: [
          // Left: Branding wrapped in Expanded with Flexible text
          Expanded(
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Image.asset(
                  'assets/images/logo2.webp',
                  height: 24,
                  fit: BoxFit.contain,
                  errorBuilder: (_, __, ___) => const Icon(Icons.business, color: Colors.white, size: 20),
                ),
                const SizedBox(width: 6),
                const Flexible(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.circle, color: Color(0xFF10B981), size: 5),
                          SizedBox(width: 3),
                          Text(
                            'PT. ASIA PLASTIK',
                            style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 0.5),
                          ),
                        ],
                      ),
                      Text(
                        'PORTAL LAYANAN TIKET',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(color: Color(0xFF94A3B8), fontSize: 8, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 6),

          // Right: User Profile Chip & Logout Button
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFF1E293B),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFF334155)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    CircleAvatar(
                      radius: 9,
                      backgroundColor: const Color(0xFF0284C7),
                      child: Text(
                        (_user?.name ?? 'K').substring(0, 1).toUpperCase(),
                        style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                      ),
                    ),
                    const SizedBox(width: 4),
                    ConstrainedBox(
                      constraints: const BoxConstraints(maxWidth: 55),
                      child: Text(
                        (_user?.name ?? 'Karyawan').split(' ').first,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ),
                    const SizedBox(width: 4),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                      decoration: BoxDecoration(color: const Color(0xFF2563EB), borderRadius: BorderRadius.circular(4)),
                      child: const Text('EMP', style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.w900)),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.logout_rounded, color: Color(0xFF94A3B8), size: 17),
                padding: const EdgeInsets.all(4),
                constraints: const BoxConstraints(minWidth: 28, minHeight: 28),
                tooltip: 'Keluar',
                onPressed: _logout,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildVariantABanner(int resolvedCount) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E3A8A), Color(0xFF0F172A)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF3B82F6).withValues(alpha: 0.4)),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.12), blurRadius: 14, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: const Color(0xFF2563EB).withValues(alpha: 0.25),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFF3B82F6).withValues(alpha: 0.4)),
                ),
                child: const Icon(Icons.confirmation_number_outlined, color: Color(0xFF93C5FD), size: 20),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(color: const Color(0xFF2563EB).withValues(alpha: 0.35), borderRadius: BorderRadius.circular(8)),
                      child: const Text('PORTAL LAYANAN TIKET KARYAWAN', style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 9, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                    ),
                    const SizedBox(height: 4),
                    const Text('Pengajuan Kendala & Bantuan Teknis', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 2),
                    const Text('Laporkan kendala operasional ke tim IT Support dan pantau status penyelesaian real-time.', style: TextStyle(color: Color(0xFF94A3B8), fontSize: 11)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // 3 Quick Action Chips
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              ElevatedButton.icon(
                onPressed: () => _scrollToKey(_formSectionKey),
                icon: const Icon(Icons.add, size: 14),
                label: const Text('Buat Tiket Baru', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
              OutlinedButton(
                onPressed: () => _scrollToKey(_queueSectionKey),
                style: OutlinedButton.styleFrom(
                  foregroundColor: Colors.white,
                  side: BorderSide(color: Colors.white.withValues(alpha: 0.25)),
                  backgroundColor: Colors.white.withValues(alpha: 0.08),
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                child: const Text('Daftar Antrean', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
              ),
              ElevatedButton.icon(
                onPressed: () => _scrollToKey(_resolvedSectionKey),
                icon: const Icon(Icons.check_circle_outline, size: 14),
                label: const Text('Riwayat Selesai', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF059669),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // VARIAN B: EXECUTIVE HUB (Segmented Modular Tabs)
  // ==========================================================================
  Widget _buildVariantB(List<TicketModel> activeTickets, List<TicketModel> resolvedTickets) {
    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Corporate Top App Bar
          _buildVariantANavbar(),

          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Quick Summary Card
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                    boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 3))],
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Halo, ${(_user?.name ?? 'Karyawan').split(' ').first} 👋', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                          const SizedBox(height: 2),
                          Text('Dept: ${_user?.departmentName ?? 'General'} • Sesi Aktif', style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                        ],
                      ),
                      Row(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.chat_bubble_outline, color: Color(0xFF10B981)),
                            tooltip: 'Hotline WhatsApp',
                            onPressed: _openWhatsAppSupport,
                          ),
                          IconButton(
                            icon: const Icon(Icons.auto_awesome, color: Color(0xFF2563EB)),
                            tooltip: 'Asisten AI',
                            onPressed: _openAiAssistantDialog,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 14),

                // Compact KPI Ribbon
                _buildKpiMetricsStrip(),
                const SizedBox(height: 18),

                // Segmented Tab Selector
                Container(
                  padding: const EdgeInsets.all(4),
                  decoration: BoxDecoration(
                    color: const Color(0xFFE2E8F0),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      Expanded(child: _segmentTabButton(0, '📝 Buat Tiket')),
                      Expanded(child: _segmentTabButton(1, '📋 Antrean (${activeTickets.length})')),
                      Expanded(child: _segmentTabButton(2, '✅ Selesai (${resolvedTickets.length})')),
                    ],
                  ),
                ),
                const SizedBox(height: 16),

                // Selected Tab Content
                if (_variantBSelectedTab == 0)
                  _buildSubmitTicketFormCard()
                else if (_variantBSelectedTab == 1)
                  _buildActiveQueueCard(activeTickets)
                else
                  _buildResolvedArchiveCard(resolvedTickets),

                const SizedBox(height: 36),
                _buildCorporateFooter(),
                const SizedBox(height: 80),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _segmentTabButton(int index, String title) {
    final isSelected = _variantBSelectedTab == index;
    return InkWell(
      onTap: () => setState(() => _variantBSelectedTab = index),
      borderRadius: BorderRadius.circular(8),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? Colors.white : Colors.transparent,
          borderRadius: BorderRadius.circular(8),
          boxShadow: isSelected ? [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 4, offset: const Offset(0, 2))] : null,
        ),
        alignment: Alignment.center,
        child: Text(
          title,
          style: TextStyle(
            fontSize: 11,
            fontWeight: isSelected ? FontWeight.w900 : FontWeight.bold,
            color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
          ),
        ),
      ),
    );
  }

  // ==========================================================================
  // VARIAN C: MODERN BENTO DASHBOARD (Action-First Industrial Layout)
  // ==========================================================================
  Widget _buildVariantC(List<TicketModel> activeTickets, List<TicketModel> resolvedTickets) {
    final total = _kpi['total_tickets'] ?? 0;
    final resolved = _kpi['resolved_tickets'] ?? 0;
    final percent = total > 0 ? (resolved / total).clamp(0.0, 1.0) : 1.0;

    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _buildVariantANavbar(),

          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Modern Bento Progress Hero
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: const Color(0xFF334155)),
                    boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.15), blurRadius: 16, offset: const Offset(0, 6))],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('PERFORMA PENANGANAN TIKET', style: TextStyle(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.6)),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(color: const Color(0xFF10B981).withValues(alpha: 0.2), borderRadius: BorderRadius.circular(10)),
                            child: Row(
                              children: [
                                const Icon(Icons.bolt, color: Color(0xFF10B981), size: 12),
                                const SizedBox(width: 4),
                                Text('${(percent * 100).toInt()}% Selesai', style: const TextStyle(color: Color(0xFF34D399), fontSize: 10, fontWeight: FontWeight.w900)),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Text('$resolved', style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: Colors.white)),
                          Text(' / $total Tiket', style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                        ],
                      ),
                      const SizedBox(height: 8),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(6),
                        child: LinearProgressIndicator(
                          value: percent,
                          minHeight: 8,
                          backgroundColor: const Color(0xFF334155),
                          color: const Color(0xFF10B981),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // 4 Mini Bento Chips
                      Row(
                        children: [
                          Expanded(child: _bentoMiniStat('Aktif', '${_kpi['active_tickets'] ?? 0}', const Color(0xFF3B82F6))),
                          const SizedBox(width: 6),
                          Expanded(child: _bentoMiniStat('Menunggu', '${_kpi['pending_tickets'] ?? 0}', const Color(0xFFF59E0B))),
                          const SizedBox(width: 6),
                          Expanded(child: _bentoMiniStat('Diproses', '${_kpi['in_progress_tickets'] ?? 0}', const Color(0xFF6366F1))),
                          const SizedBox(width: 6),
                          Expanded(child: _bentoMiniStat('Selesai', '${_kpi['resolved_tickets'] ?? 0}', const Color(0xFF10B981))),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),

                // AI Smart Preset Guidance Card
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFBFDBFE)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.auto_awesome, color: Color(0xFF2563EB), size: 24),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('Butuh Bantuan Kendala Cepat?', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF1E3A8A))),
                            const Text('Gunakan tombol AI perapih deskripsi atau hubungi support langsung.', style: TextStyle(fontSize: 10, color: Color(0xFF3B82F6))),
                          ],
                        ),
                      ),
                      TextButton(
                        onPressed: _openAiAssistantDialog,
                        style: TextButton.styleFrom(backgroundColor: const Color(0xFF2563EB), foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6)),
                        child: const Text('Buka AI', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),

                // Form Pengajuan
                _buildSubmitTicketFormCard(),
                const SizedBox(height: 20),

                // Antrean Tiket Aktif
                _buildActiveQueueCard(activeTickets),
                const SizedBox(height: 20),

                // Riwayat Tiket Selesai
                _buildResolvedArchiveCard(resolvedTickets),
                const SizedBox(height: 36),

                _buildCorporateFooter(),
                const SizedBox(height: 80),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _bentoMiniStat(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 8),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Column(
        children: [
          Text(value, style: TextStyle(color: color, fontSize: 13, fontWeight: FontWeight.w900)),
          Text(label, style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 9)),
        ],
      ),
    );
  }

  // ==========================================================================
  // SHARED KPI METRICS STRIP
  // ==========================================================================
  Widget _buildKpiMetricsStrip() {
    return LayoutBuilder(builder: (context, constraints) {
      final isWide = constraints.maxWidth > 700;
      final cards = [
        _kpiMetricCard(
          title: 'TIKET AKTIF',
          value: '${_kpi['active_tickets'] ?? 0}',
          icon: Icons.bolt_rounded,
          iconColor: const Color(0xFF2563EB),
          bgColor: const Color(0xFFEFF6FF),
        ),
        _kpiMetricCard(
          title: 'MENUNGGU',
          value: '${_kpi['pending_tickets'] ?? 0}',
          icon: Icons.access_time_rounded,
          iconColor: const Color(0xFFD97706),
          bgColor: const Color(0xFFFFFBEB),
        ),
        _kpiMetricCard(
          title: 'DIPROSES',
          value: '${_kpi['in_progress_tickets'] ?? 0}',
          icon: Icons.sync_rounded,
          iconColor: const Color(0xFF2563EB),
          bgColor: const Color(0xFFEFF6FF),
        ),
        _kpiMetricCard(
          title: 'TERSELESAIKAN',
          value: '${_kpi['resolved_tickets'] ?? 0}',
          icon: Icons.check_circle_rounded,
          iconColor: const Color(0xFF10B981),
          bgColor: const Color(0xFFECFDF5),
        ),
      ];

      if (isWide) {
        return Row(
          children: cards.map((c) => Expanded(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 4), child: c))).toList(),
        );
      } else {
        return GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 2.2,
          children: cards,
        );
      }
    });
  }

  Widget _kpiMetricCard({
    required String title,
    required String value,
    required IconData icon,
    required Color iconColor,
    required Color bgColor,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF64748B),
                  letterSpacing: 0.5,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w900,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.all(7),
            decoration: BoxDecoration(
              color: bgColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: iconColor, size: 18),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // SHARED FORM: SUBMIT SUPPORT REQUEST
  // ==========================================================================
  Widget _buildSubmitTicketFormCard() {
    final senderDeptName = _departments.firstWhere((d) => d.id == _selectedSenderDeptId, orElse: () => DepartmentModel(id: 0, name: _user?.departmentName ?? 'IT Support')).name;
    final availableCategories = _categoriesMap[_departments.firstWhere((d) => d.id == _selectedTargetDeptId, orElse: () => DepartmentModel(id: 0, name: 'IT Support')).name] ?? [
      'Jaringan & Internet',
      'Hardware & PC',
      'Software & OS',
      'Printer & Scanner',
      'Email & Akun',
      'Lainnya',
    ];

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 16, offset: const Offset(0, 4)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Header Bar
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFBFDBFE)),
                  ),
                  child: const Icon(Icons.description_outlined, color: Color(0xFF2563EB), size: 18),
                ),
                const SizedBox(width: 10),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Submit Support Request',
                        style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                      ),
                      Text(
                        'Dispatch official ticket to designated corporate department',
                        style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: const Color(0xFFBFDBFE)),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.circle, color: Color(0xFF2563EB), size: 6),
                      SizedBox(width: 4),
                      Text(
                        'Live Routing',
                        style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFF1F5F9)),

          if (_ticketFormSuccessMsg != null)
            Container(
              margin: const EdgeInsets.fromLTRB(16, 14, 16, 0),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFECFDF5),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFA7F3D0)),
              ),
              child: Row(
                children: [
                  const Icon(Icons.check_circle, color: Color(0xFF059669), size: 18),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(_ticketFormSuccessMsg!, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF065F46))),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, size: 14, color: Color(0xFF065F46)),
                    onPressed: () => setState(() => _ticketFormSuccessMsg = null),
                  ),
                ],
              ),
            ),

          // Form Fields
          Padding(
            padding: const EdgeInsets.all(16),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  _fieldLabel('JUDUL PERMINTAAN'),
                  const SizedBox(height: 6),
                  TextFormField(
                    controller: _titleController,
                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                    decoration: _inputDecoration('Contoh: Gangguan koneksi VPN / Printer Offline di Lantai 2'),
                    validator: (val) {
                      if (val == null || val.trim().isEmpty) return 'Judul tiket wajib diisi';
                      return null;
                    },
                  ),
                  const SizedBox(height: 14),

                  // Dropdowns
                  LayoutBuilder(builder: (context, constraints) {
                    final isWide = constraints.maxWidth > 650;
                    final senderDropdown = Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _fieldLabel('DEPARTEMEN PENGIRIM'),
                        const SizedBox(height: 6),
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
                                child: Text(senderDeptName, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0F172A))),
                              ),
                            ],
                          ),
                        ),
                      ],
                    );

                    final targetDropdown = Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _fieldLabel('DEPARTEMEN TUJUAN'),
                        const SizedBox(height: 6),
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
                      ],
                    );

                    final categoryDropdown = Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _fieldLabel('KATEGORI PERMINTAAN'),
                        const SizedBox(height: 6),
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
                      ],
                    );

                    if (isWide) {
                      return Row(
                        children: [
                          Expanded(child: senderDropdown),
                          const SizedBox(width: 10),
                          Expanded(child: targetDropdown),
                          const SizedBox(width: 10),
                          Expanded(child: categoryDropdown),
                        ],
                      );
                    } else {
                      return Column(
                        children: [
                          senderDropdown,
                          const SizedBox(height: 10),
                          targetDropdown,
                          const SizedBox(height: 10),
                          categoryDropdown,
                        ],
                      );
                    }
                  }),
                  const SizedBox(height: 14),

                  // Description
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
                  const SizedBox(height: 6),
                  TextFormField(
                    controller: _descController,
                    minLines: 3,
                    maxLines: 6,
                    style: const TextStyle(fontSize: 12, color: Color(0xFF0F172A)),
                    onChanged: (_) => setState(() {}),
                    decoration: _inputDecoration('Jelaskan detail kendala, nomor aset, pesan error yang tampil...'),
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
                      Text(
                        '${_descController.text.length} karakter (Min. 10)',
                        style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8)),
                      ),
                      const Text('Format: Teks / Template Otomatis', style: TextStyle(fontSize: 10, color: Color(0xFF94A3B8))),
                    ],
                  ),
                  const SizedBox(height: 14),

                  // Photo Attachment
                  _fieldLabel('BUKTI FOTO / LAMPIRAN (OPSIONAL)'),
                  const SizedBox(height: 6),
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
                      padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 14),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF8FAFC),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFCBD5E1)),
                      ),
                      child: _imageBytes == null
                          ? const Column(
                              children: [
                                Icon(Icons.add_photo_alternate_outlined, color: Color(0xFF2563EB), size: 26),
                                SizedBox(height: 6),
                                Text('Pilih Bukti Foto atau Sentuh di Sini', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Color(0xFF2563EB))),
                                Text('(tangkapan layar error, fisik alat, dsb. Maks 10MB)', style: TextStyle(fontSize: 9, color: Color(0xFF94A3B8))),
                              ],
                            )
                          : Row(
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(8),
                                  child: Image.memory(_imageBytes!, width: 48, height: 48, fit: BoxFit.cover),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(_imageFileName ?? 'Foto Terlampir', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                      const Text('Siap diunggah bersama tiket', style: TextStyle(fontSize: 10, color: Color(0xFF10B981))),
                                    ],
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline, color: Color(0xFFEF4444), size: 20),
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
                  const SizedBox(height: 18),

                  ElevatedButton.icon(
                    onPressed: _isSubmittingTicket ? null : _submitTicket,
                    icon: _isSubmittingTicket
                        ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Icon(Icons.send_rounded, size: 14),
                    label: Text(_isSubmittingTicket ? 'Mengirim...' : 'Kirim Permintaan Tiket', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 12)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2563EB),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 13),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // SHARED CARD: ANTREAN TIKET & INSIDEN AKTIF
  // ==========================================================================
  Widget _buildActiveQueueCard(List<TicketModel> tickets) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 16, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFF6FF),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFBFDBFE)),
                      ),
                      child: const Icon(Icons.inbox_outlined, color: Color(0xFF2563EB), size: 18),
                    ),
                    const SizedBox(width: 10),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Antrean Tiket & Insiden Aktif', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A))),
                          Text('Pilih tiket untuk membuka detail lengkap dan riwayat pengerjaan', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                TextField(
                  onChanged: (val) => setState(() => _activeQueueSearch = val),
                  style: const TextStyle(fontSize: 12),
                  decoration: InputDecoration(
                    hintText: 'Cari judul atau rincian tiket...',
                    hintStyle: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                    prefixIcon: const Icon(Icons.search, size: 16, color: Color(0xFF94A3B8)),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                    isDense: true,
                    filled: true,
                    fillColor: Colors.white,
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFF1F5F9)),

          // Status & Department Filter Bar
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            child: Wrap(
              alignment: WrapAlignment.spaceBetween,
              crossAxisAlignment: WrapCrossAlignment.center,
              spacing: 8,
              runSpacing: 8,
              children: [
                Wrap(
                  spacing: 6,
                  runSpacing: 4,
                  crossAxisAlignment: WrapCrossAlignment.center,
                  children: [
                    const Text('STATUS:', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Color(0xFF64748B))),
                    _statusPill('Semua'),
                    _statusPill('Pending'),
                    _statusPill('Open'),
                    _statusPill('In Progress'),
                    _statusPill('Resolved'),
                  ],
                ),
                DropdownButton<int?>(
                  value: _activeQueueDeptFilter,
                  isDense: true,
                  hint: const Text('Semua Departemen', style: TextStyle(fontSize: 11)),
                  underline: const SizedBox(),
                  style: const TextStyle(fontSize: 11, color: Color(0xFF0F172A), fontWeight: FontWeight.bold),
                  items: [
                    const DropdownMenuItem<int?>(value: null, child: Text('Semua Departemen')),
                    ..._departments.map((d) => DropdownMenuItem<int?>(value: d.id, child: Text(d.name))),
                  ],
                  onChanged: (val) => setState(() => _activeQueueDeptFilter = val),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFF1F5F9)),

          if (tickets.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 36),
              child: Center(
                child: Text('Tidak ada tiket dalam antrean saat ini.', style: TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
              ),
            )
          else
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              itemCount: tickets.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (context, index) {
                return _buildTicketCard(tickets[index], isResolvedSection: false);
              },
            ),
        ],
      ),
    );
  }

  Widget _statusPill(String status) {
    final isSelected = _activeQueueStatus == status;
    return InkWell(
      onTap: () => setState(() => _activeQueueStatus = status),
      borderRadius: BorderRadius.circular(6),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2563EB) : const Color(0xFFF1F5F9),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Text(
          status,
          style: TextStyle(
            color: isSelected ? Colors.white : const Color(0xFF475569),
            fontSize: 10,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  // ==========================================================================
  // SHARED CARD: RIWAYAT TIKET SAYA YANG SELESAI
  // ==========================================================================
  Widget _buildResolvedArchiveCard(List<TicketModel> resolvedTickets) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 16, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: const Color(0xFFECFDF5),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFA7F3D0)),
                      ),
                      child: const Icon(Icons.verified_outlined, color: Color(0xFF059669), size: 18),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            spacing: 6,
                            runSpacing: 4,
                            children: [
                              const Text('Riwayat Tiket Saya yang Selesai', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A))),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(color: const Color(0xFFECFDF5), borderRadius: BorderRadius.circular(10), border: Border.all(color: const Color(0xFFA7F3D0))),
                                child: Text('${resolvedTickets.length} SELESAI', style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Color(0xFF065F46))),
                              ),
                            ],
                          ),
                          const SizedBox(height: 2),
                          const Text('Daftar riwayat tiket yang telah selesai diproses.', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                // Search & Filter
                Wrap(
                  spacing: 10,
                  runSpacing: 8,
                  crossAxisAlignment: WrapCrossAlignment.center,
                  children: [
                    SizedBox(
                      width: 220,
                      child: TextField(
                        onChanged: (val) => setState(() => _resolvedSearch = val),
                        style: const TextStyle(fontSize: 11),
                        decoration: InputDecoration(
                          hintText: 'Cari tiket selesai...',
                          hintStyle: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                          prefixIcon: const Icon(Icons.search, size: 14, color: Color(0xFF94A3B8)),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                          isDense: true,
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
                        ),
                      ),
                    ),
                    DropdownButton<int?>(
                      value: _resolvedDeptFilter,
                      isDense: true,
                      hint: const Text('Semua Departemen', style: TextStyle(fontSize: 11)),
                      underline: const SizedBox(),
                      style: const TextStyle(fontSize: 11, color: Color(0xFF0F172A), fontWeight: FontWeight.bold),
                      items: [
                        const DropdownMenuItem<int?>(value: null, child: Text('Semua Departemen')),
                        ..._departments.map((d) => DropdownMenuItem<int?>(value: d.id, child: Text(d.name))),
                      ],
                      onChanged: (val) => setState(() => _resolvedDeptFilter = val),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFF1F5F9)),

          if (resolvedTickets.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 36),
              child: Center(
                child: Text('Belum ada tiket yang berstatus selesai.', style: TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
              ),
            )
          else
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              itemCount: resolvedTickets.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (context, index) {
                return _buildTicketCard(resolvedTickets[index], isResolvedSection: true);
              },
            ),
        ],
      ),
    );
  }

  // ==========================================================================
  // SHARED TICKET CARD WIDGET
  // ==========================================================================
  Widget _buildTicketCard(TicketModel ticket, {required bool isResolvedSection}) {
    String formattedDate = ticket.createdAtHuman ?? ticket.createdAt ?? '-';
    if (ticket.createdAt != null) {
      final parsed = DateTime.tryParse(ticket.createdAt!);
      if (parsed != null) {
        formattedDate = DateFormat('dd/MM/yyyy, HH:mm').format(parsed);
      }
    }

    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => TicketDetailScreen(ticketId: ticket.id)),
        ).then((_) => _loadAllData());
      },
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 6, offset: const Offset(0, 2)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.calendar_today_outlined, size: 11, color: Color(0xFF94A3B8)),
                    const SizedBox(width: 4),
                    Text(formattedDate, style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8))),
                  ],
                ),
                Text('#${ticket.id}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8))),
              ],
            ),
            const SizedBox(height: 6),
            Text(
              ticket.title.toUpperCase(),
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF0F172A), letterSpacing: 0.3),
            ),
            const SizedBox(height: 4),
            Text(
              ticket.description,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), height: 1.4),
            ),
            const SizedBox(height: 10),

            Wrap(
              alignment: WrapAlignment.spaceBetween,
              crossAxisAlignment: WrapCrossAlignment.center,
              spacing: 8,
              runSpacing: 6,
              children: [
                Wrap(
                  spacing: 6,
                  runSpacing: 4,
                  crossAxisAlignment: WrapCrossAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(color: const Color(0xFFCBD5E1)),
                      ),
                      child: Text(
                        ticket.targetDepartmentName ?? 'IT Support',
                        style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF334155)),
                      ),
                    ),
                    _badgeForStatus(ticket.status),
                    const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.verified, size: 11, color: Color(0xFF10B981)),
                        SizedBox(width: 2),
                        Text('Di-ACC Admin', style: TextStyle(fontSize: 9, color: Color(0xFF10B981), fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ],
                ),

                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      isResolvedSection ? 'Detail Riwayat & Solusi' : 'Buka Laporan',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: isResolvedSection ? const Color(0xFF059669) : const Color(0xFF2563EB),
                      ),
                    ),
                    Icon(
                      Icons.chevron_right,
                      size: 14,
                      color: isResolvedSection ? const Color(0xFF059669) : const Color(0xFF2563EB),
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _badgeForStatus(String status) {
    Color bg = const Color(0xFFEFF6FF);
    Color text = const Color(0xFF1D4ED8);
    Color border = const Color(0xFFBFDBFE);

    final lower = status.toLowerCase();
    if (lower == 'pending') {
      bg = const Color(0xFFFFFBEB);
      text = const Color(0xFFD97706);
      border = const Color(0xFFFDE68A);
    } else if (lower == 'in progress') {
      bg = const Color(0xFFFAF5FF);
      text = const Color(0xFF7C3AED);
      border = const Color(0xFFDDD6FE);
    } else if (lower == 'resolved' || lower == 'closed') {
      bg = const Color(0xFFECFDF5);
      text = const Color(0xFF059669);
      border = const Color(0xFFA7F3D0);
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6), border: Border.all(color: border)),
      child: Text(status, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: text)),
    );
  }

  Widget _buildCorporateFooter() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
      decoration: const BoxDecoration(border: Border(top: BorderSide(color: Color(0xFFE2E8F0)))),
      child: const Column(
        children: [
          Wrap(
            alignment: WrapAlignment.center,
            crossAxisAlignment: WrapCrossAlignment.center,
            children: [
              Text('PT. ASIA PLASTIK', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Color(0xFF475569))),
              Text(' • Sistem Manajemen Tiket & Komunikasi Internal Manufaktur', style: TextStyle(fontSize: 10, color: Color(0xFF64748B))),
            ],
          ),
          SizedBox(height: 4),
          Text(
            'Kantor & Pabrik: Rungkut Industri, Surabaya • Telp: +6231 8433078 • © 2026 All Rights Reserved',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 9, color: Color(0xFF94A3B8)),
          ),
        ],
      ),
    );
  }

  void _logout() async {
    await ApiService.logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginScreen()), (r) => false);
  }

  Widget _fieldLabel(String text) {
    return Text(text, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w800, color: Color(0xFF334155), letterSpacing: 0.5));
  }

  InputDecoration _inputDecoration(String hint) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
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
