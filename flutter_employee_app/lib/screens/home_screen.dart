import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/department_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'ticket_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  final Function(int)? onNavigateToTab;

  const HomeScreen({
    super.key,
    this.onNavigateToTab,
  });

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ScrollController _scrollController = ScrollController();

  UserModel? _user;
  Map<String, int> _kpi = {
    'total_tickets': 0,
    'pending_tickets': 0,
    'in_progress_tickets': 0,
    'resolved_tickets': 0,
    'active_tickets': 0,
  };

  List<DepartmentModel> _departments = [];
  List<TicketModel> _allTickets = [];
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
    super.dispose();
  }

  Future<void> _loadAllData() async {
    final user = await ApiService.getSavedUser();
    final depts = await ApiService.getDepartments();
    final kpi = await ApiService.getEmployeeKpi();
    final tickets = await ApiService.getEmployeeTickets();

    if (!mounted) return;

    setState(() {
      _user = user;
      _departments = depts;
      _kpi = kpi;
      _allTickets = tickets;
      _isLoading = false;
    });
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
      body: RefreshIndicator(
        onRefresh: _loadAllData,
        color: const Color(0xFF2563EB),
        child: SingleChildScrollView(
          controller: _scrollController,
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // ==========================================================
              // 1. TOP CORPORATE NAVBAR
              // ==========================================================
              _buildCorporateNavbar(),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // ====================================================
                    // 2. USER GREETING CARD (GAMBAR 1)
                    // ====================================================
                    _buildGreetingCard(),
                    const SizedBox(height: 14),

                    // ====================================================
                    // 3. 4 KPI STAT CARDS (GAMBAR 1)
                    // ====================================================
                    _buildKpiMetricsStrip(),
                    const SizedBox(height: 18),

                    // ====================================================
                    // 4. RIWAYAT TIKET SAYA YANG SELESAI (GAMBAR 2)
                    // ====================================================
                    _buildResolvedArchiveCard(resolvedTickets),
                    const SizedBox(height: 24),

                    // ====================================================
                    // 5. CORPORATE FOOTER
                    // ====================================================
                    _buildCorporateFooter(),
                    const SizedBox(height: 80), // Ample spacing for bottom floating pill nav
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ==========================================================================
  // TOP CORPORATE NAVBAR
  // ==========================================================================
  Widget _buildCorporateNavbar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: const BoxDecoration(
        color: Color(0xFF0F172A),
        border: Border(bottom: BorderSide(color: Color(0xFF1E293B))),
      ),
      child: Row(
        children: [
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

  // ==========================================================================
  // USER GREETING CARD (GAMBAR 1)
  // ==========================================================================
  Widget _buildGreetingCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 20,
            backgroundColor: const Color(0xFF2563EB).withValues(alpha: 0.1),
            child: Text(
              (_user?.name ?? 'K').substring(0, 1).toUpperCase(),
              style: const TextStyle(color: Color(0xFF2563EB), fontWeight: FontWeight.w900, fontSize: 16),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _user?.name ?? 'Kevina Maydiva Heriansaputri',
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                ),
                const SizedBox(height: 2),
                Text(
                  '${_user?.departmentName ?? 'IT Support'} • Portal Layanan Tiket',
                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: const Color(0xFFECFDF5),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: const Color(0xFFA7F3D0)),
            ),
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.circle, color: Color(0xFF10B981), size: 6),
                SizedBox(width: 4),
                Text(
                  'Online',
                  style: TextStyle(color: Color(0xFF065F46), fontSize: 11, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // 4 KPI STAT CARDS (GAMBAR 1)
  // ==========================================================================
  Widget _buildKpiMetricsStrip() {
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
        borderRadius: BorderRadius.circular(16),
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
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.all(7),
            decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: iconColor, size: 18),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // RIWAYAT TIKET SAYA YANG SELESAI (GAMBAR 2)
  // ==========================================================================
  Widget _buildResolvedArchiveCard(List<TicketModel> resolvedTickets) {
    return Container(
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
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(7),
                      decoration: BoxDecoration(
                        color: const Color(0xFFECFDF5),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: const Color(0xFFA7F3D0)),
                      ),
                      child: const Icon(Icons.verified_outlined, color: Color(0xFF059669), size: 18),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            spacing: 6,
                            runSpacing: 4,
                            children: [
                              const Text(
                                'Riwayat Tiket Saya yang Selesai',
                                style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                              ),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFECFDF5),
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(color: const Color(0xFFA7F3D0)),
                                ),
                                child: Text(
                                  '${resolvedTickets.length} SELESAI',
                                  style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Color(0xFF065F46)),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 2),
                          const Text(
                            'Daftar riwayat tiket yang telah selesai diproses.',
                            style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),

                // Search & Filter Row
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
                          contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                          isDense: true,
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(8),
                            borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
                          ),
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

          // List of Resolved Tickets
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
              padding: const EdgeInsets.all(14),
              itemCount: resolvedTickets.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (context, index) {
                final ticket = resolvedTickets[index];
                return _buildResolvedTicketCard(ticket);
              },
            ),
        ],
      ),
    );
  }

  Widget _buildResolvedTicketCard(TicketModel ticket) {
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
            BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 4, offset: const Offset(0, 2)),
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
                        borderRadius: BorderRadius.circular(4),
                        border: Border.all(color: const Color(0xFFCBD5E1)),
                      ),
                      child: Text(
                        ticket.targetDepartmentName ?? 'IT Support',
                        style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF334155)),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFFECFDF5),
                        borderRadius: BorderRadius.circular(4),
                        border: Border.all(color: const Color(0xFFA7F3D0)),
                      ),
                      child: const Text('Resolved', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: Color(0xFF059669))),
                    ),
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

                const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text('Detail Solusi', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF059669))),
                    Icon(Icons.chevron_right, size: 14, color: Color(0xFF059669)),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // ==========================================================================
  // CORPORATE FOOTER
  // ==========================================================================
  Widget _buildCorporateFooter() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 14),
      decoration: const BoxDecoration(
        border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
      ),
      child: const Column(
        children: [
          Wrap(
            alignment: WrapAlignment.center,
            crossAxisAlignment: WrapCrossAlignment.center,
            children: [
              Text('PT. ASIA PLASTIK', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Color(0xFF475569))),
              Text(' • Sistem Manajemen Tiket Manufaktur', style: TextStyle(fontSize: 10, color: Color(0xFF64748B))),
            ],
          ),
          SizedBox(height: 3),
          Text(
            'Rungkut Industri Surabaya • Telp: +6231 8433078 • © 2026 All Rights Reserved',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 9, color: Color(0xFF94A3B8)),
          ),
        ],
      ),
    );
  }
}
