import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/department_model.dart';
import '../models/ticket_model.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'ticket_detail_screen.dart';

class TicketListScreen extends StatefulWidget {
  const TicketListScreen({super.key});

  @override
  State<TicketListScreen> createState() => _TicketListScreenState();
}

class _TicketListScreenState extends State<TicketListScreen> {
  List<DepartmentModel> _departments = [];
  List<TicketModel> _tickets = [];

  String _searchQuery = '';
  String _selectedStatus = 'Semua';
  int? _selectedDepartmentId;

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final depts = await ApiService.getDepartments();
    final tickets = await ApiService.getEmployeeTickets();

    if (!mounted) return;

    setState(() {
      _departments = depts;
      _tickets = tickets;
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

    final filteredTickets = _tickets.where((t) {
      final matchesSearch = _searchQuery.isEmpty ||
          t.title.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          t.description.toLowerCase().contains(_searchQuery.toLowerCase());
      final matchesStatus = _selectedStatus == 'Semua' ||
          t.status.toLowerCase() == _selectedStatus.toLowerCase();
      final matchesDept = _selectedDepartmentId == null || t.targetDepartmentId == _selectedDepartmentId;
      return matchesSearch && matchesStatus && matchesDept;
    }).toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _loadData,
          color: const Color(0xFF2563EB),
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
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
                      // ======================================================
                      // ANTREAN TIKET & INSIDEN AKTIF CARD (GAMBAR 4)
                      // ======================================================
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
                              padding: const EdgeInsets.all(14),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.stretch,
                                children: [
                                  Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(7),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFEFF6FF),
                                          borderRadius: BorderRadius.circular(8),
                                          border: Border.all(color: const Color(0xFFBFDBFE)),
                                        ),
                                        child: const Icon(Icons.inbox_outlined, color: Color(0xFF2563EB), size: 18),
                                      ),
                                      const SizedBox(width: 8),
                                      const Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              'Antrean Tiket & Insiden Aktif',
                                              style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                                            ),
                                            Text(
                                              'Pilih tiket untuk membuka detail lengkap dan riwayat penanganan',
                                              style: TextStyle(fontSize: 10, color: Color(0xFF64748B)),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 10),

                                  // Search Field
                                  TextField(
                                    onChanged: (val) => setState(() => _searchQuery = val),
                                    style: const TextStyle(fontSize: 11),
                                    decoration: InputDecoration(
                                      hintText: 'Cari judul atau rincian tiket...',
                                      hintStyle: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                                      prefixIcon: const Icon(Icons.search, size: 16, color: Color(0xFF94A3B8)),
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
                                ],
                              ),
                            ),
                            const Divider(height: 1, color: Color(0xFFF1F5F9)),

                            // Filter Pills & Dropdown
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                              child: Wrap(
                                alignment: WrapAlignment.spaceBetween,
                                crossAxisAlignment: WrapCrossAlignment.center,
                                spacing: 6,
                                runSpacing: 6,
                                children: [
                                  Wrap(
                                    spacing: 4,
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
                                    value: _selectedDepartmentId,
                                    isDense: true,
                                    hint: const Text('Semua Departemen', style: TextStyle(fontSize: 10)),
                                    underline: const SizedBox(),
                                    style: const TextStyle(fontSize: 10, color: Color(0xFF0F172A), fontWeight: FontWeight.bold),
                                    items: [
                                      const DropdownMenuItem<int?>(value: null, child: Text('Semua Departemen')),
                                      ..._departments.map((d) => DropdownMenuItem<int?>(value: d.id, child: Text(d.name))),
                                    ],
                                    onChanged: (val) => setState(() => _selectedDepartmentId = val),
                                  ),
                                ],
                              ),
                            ),
                            const Divider(height: 1, color: Color(0xFFF1F5F9)),

                            // Ticket List
                            if (filteredTickets.isEmpty)
                              const Padding(
                                padding: EdgeInsets.symmetric(vertical: 36),
                                child: Center(
                                  child: Text('Tidak ada tiket dalam antrean saat ini.', style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8))),
                                ),
                              )
                            else
                              ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                padding: const EdgeInsets.all(14),
                                itemCount: filteredTickets.length,
                                separatorBuilder: (_, __) => const SizedBox(height: 8),
                                itemBuilder: (context, index) {
                                  final ticket = filteredTickets[index];
                                  return _buildActiveTicketCard(ticket);
                                },
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
      ),
    );
  }

  Widget _statusPill(String status) {
    final isSelected = _selectedStatus == status;
    return InkWell(
      onTap: () => setState(() => _selectedStatus = status),
      borderRadius: BorderRadius.circular(6),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2563EB) : const Color(0xFFF1F5F9),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Text(
          status,
          style: TextStyle(
            color: isSelected ? Colors.white : const Color(0xFF475569),
            fontSize: 9,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  Widget _buildActiveTicketCard(TicketModel ticket) {
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
        ).then((_) => _loadData());
      },
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
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
                    const Icon(Icons.calendar_today_outlined, size: 10, color: Color(0xFF94A3B8)),
                    const SizedBox(width: 4),
                    Text(formattedDate, style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8))),
                  ],
                ),
                Text('#${ticket.id}', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8))),
              ],
            ),
            const SizedBox(height: 5),
            Text(
              ticket.title.toUpperCase(),
              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Color(0xFF0F172A), letterSpacing: 0.3),
            ),
            const SizedBox(height: 3),
            Text(
              ticket.description,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 10, color: Color(0xFF64748B), height: 1.4),
            ),
            const SizedBox(height: 8),

            Wrap(
              alignment: WrapAlignment.spaceBetween,
              crossAxisAlignment: WrapCrossAlignment.center,
              spacing: 6,
              runSpacing: 4,
              children: [
                Wrap(
                  spacing: 4,
                  runSpacing: 4,
                  crossAxisAlignment: WrapCrossAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
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
                    _badgeForStatus(ticket.status),
                    const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.verified, size: 10, color: Color(0xFF10B981)),
                        SizedBox(width: 2),
                        Text('Di-ACC Admin', style: TextStyle(fontSize: 8, color: Color(0xFF10B981), fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ],
                ),

                const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      'Buka Laporan',
                      style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF2563EB)),
                    ),
                    Icon(Icons.chevron_right, size: 14, color: Color(0xFF2563EB)),
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
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(4), border: Border.all(color: border)),
      child: Text(status, style: TextStyle(fontSize: 8, fontWeight: FontWeight.w800, color: text)),
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
}
