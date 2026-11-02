import 'package:flutter/material.dart';
import '../models/department_model.dart';
import '../models/ticket_model.dart';
import '../services/api_service.dart';
import 'hrd_ticket_detail_screen.dart';

class GlobalTicketsScreen extends StatefulWidget {
  const GlobalTicketsScreen({super.key});

  @override
  State<GlobalTicketsScreen> createState() => _GlobalTicketsScreenState();
}

class _GlobalTicketsScreenState extends State<GlobalTicketsScreen> {
  final _searchController = TextEditingController();
  List<DepartmentModel> _departments = [];
  int? _selectedDeptId;
  String _selectedStatus = 'Semua';
  List<TicketModel> _tickets = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadInitialData() async {
    final depts = await ApiService.getDepartments();
    if (!mounted) return;
    setState(() => _departments = depts);
    _loadTickets();
  }

  Future<void> _loadTickets() async {
    setState(() => _isLoading = true);
    final tickets = await ApiService.getGlobalTickets(
      departmentId: _selectedDeptId,
      status: _selectedStatus == 'Semua' ? null : _selectedStatus,
      search: _searchController.text.trim().isEmpty ? null : _searchController.text.trim(),
    );

    if (!mounted) return;

    setState(() {
      _tickets = tickets;
      _isLoading = false;
    });
  }

  void _showStatusDialog(TicketModel ticket) {
    String newStatus = ticket.status;
    final noteController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text('Update Status Tiket #${ticket.id}', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Pilih status pengerjaan:', style: TextStyle(fontSize: 12, color: Colors.grey)),
              const SizedBox(height: 8),
              DropdownButtonFormField<String>(
                value: newStatus,
                decoration: const InputDecoration(border: OutlineInputBorder(), contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8)),
                items: ['Pending', 'In Progress', 'Resolved'].map((s) => DropdownMenuItem(value: s, child: Text(s, style: const TextStyle(fontSize: 13)))).toList(),
                onChanged: (val) {
                  if (val != null) setDialogState(() => newStatus = val);
                },
              ),
              const SizedBox(height: 12),
              TextField(
                controller: noteController,
                maxLines: 2,
                decoration: const InputDecoration(
                  labelText: 'Catatan Penanganan (Opsional)',
                  labelStyle: TextStyle(fontSize: 12),
                  hintText: 'Contoh: Masalah telah diatasi oleh teknisi.',
                  border: OutlineInputBorder(),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF4F46E5), foregroundColor: Colors.white),
              onPressed: () async {
                Navigator.pop(ctx);
                final res = await ApiService.updateTicketStatus(ticket.id, newStatus, note: noteController.text.trim());
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(res['message'] ?? 'Status berhasil diubah')),
                  );
                  _loadTickets();
                }
              },
              child: const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text(
          'Tangani Tiket Masuk',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: false,
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Filter Bar
            Container(
              color: Colors.white,
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
              child: Column(
                children: [
                  TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari judul, pengaju, atau kendala...',
                      hintStyle: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                      prefixIcon: const Icon(Icons.search_rounded, size: 20, color: Color(0xFF64748B)),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                _loadTickets();
                              },
                            )
                          : null,
                      filled: true,
                      fillColor: const Color(0xFFF1F5F9),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    ),
                    onSubmitted: (_) => _loadTickets(),
                  ),
                  const SizedBox(height: 8),

                  Row(
                    children: [
                      // Department Selector
                      Expanded(
                        child: DropdownButtonFormField<int?>(
                          value: _selectedDeptId,
                          decoration: InputDecoration(
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                            filled: true,
                            fillColor: const Color(0xFFF1F5F9),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                          ),
                          hint: const Text('Semua Divisi', style: TextStyle(fontSize: 11)),
                          items: [
                            const DropdownMenuItem<int?>(value: null, child: Text('Semua Divisi', style: TextStyle(fontSize: 11))),
                            ..._departments.map((d) => DropdownMenuItem<int?>(value: d.id, child: Text(d.name, style: const TextStyle(fontSize: 11)))),
                          ],
                          onChanged: (val) {
                            setState(() => _selectedDeptId = val);
                            _loadTickets();
                          },
                        ),
                      ),
                      const SizedBox(width: 8),

                      // Status Selector
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: _selectedStatus,
                          decoration: InputDecoration(
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                            filled: true,
                            fillColor: const Color(0xFFF1F5F9),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                          ),
                          items: ['Semua', 'Pending', 'In Progress', 'Resolved'].map((s) => DropdownMenuItem(value: s, child: Text(s, style: const TextStyle(fontSize: 11)))).toList(),
                          onChanged: (val) {
                            if (val != null) {
                              setState(() => _selectedStatus = val);
                              _loadTickets();
                            }
                          },
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Tickets List
            Expanded(
              child: RefreshIndicator(
                onRefresh: _loadTickets,
                color: const Color(0xFF4F46E5),
                child: _isLoading
                    ? const Center(child: CircularProgressIndicator())
                    : _tickets.isEmpty
                        ? Center(
                            child: SingleChildScrollView(
                              physics: const AlwaysScrollableScrollPhysics(),
                              padding: const EdgeInsets.all(24),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.checklist_rtl_rounded, size: 48, color: Colors.grey.shade400),
                                  const SizedBox(height: 12),
                                  const Text('Tidak Ada Tiket Masuk', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF334155))),
                                  const SizedBox(height: 4),
                                  const Text('Semua antrean tiket saat ini bersih.', style: TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
                                ],
                              ),
                            ),
                          )
                        : ListView.builder(
                            padding: const EdgeInsets.all(16),
                            itemCount: _tickets.length,
                            itemBuilder: (ctx, index) => _globalTicketCard(_tickets[index]),
                          ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _globalTicketCard(TicketModel ticket) {
    Color statusBg = const Color(0xFFFEF3C7);
    Color statusText = const Color(0xFFB45309);
    if (ticket.status == 'In Progress') {
      statusBg = const Color(0xFFEFF6FF);
      statusText = const Color(0xFF1D4ED8);
    } else if (ticket.status == 'Resolved') {
      statusBg = const Color(0xFFECFDF5);
      statusText = const Color(0xFF047857);
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 4, offset: const Offset(0, 2)),
        ],
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => HrdTicketDetailScreen(ticketId: ticket.id)),
          ).then((_) => _loadTickets());
        },
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Sender info & Status Pill
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 12,
                        backgroundColor: const Color(0xFF4F46E5),
                        backgroundImage: ticket.senderAvatar != null ? NetworkImage(ticket.senderAvatar!) : null,
                        child: ticket.senderAvatar == null ? const Icon(Icons.person, size: 14, color: Colors.white) : null,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        ticket.senderName ?? 'Karyawan',
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                      ),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(color: statusBg, borderRadius: BorderRadius.circular(8)),
                    child: Text(ticket.status, style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: statusText)),
                  ),
                ],
              ),
              const SizedBox(height: 8),

              // Title
              Text(
                ticket.title,
                style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: Color(0xFF0F172A)),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 4),

              // Description
              Text(
                ticket.description,
                style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 12),

              // Footer: Target Dept, Priority, & Quick Status Button
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6)),
                        child: Text(ticket.targetDepartmentName ?? 'IT', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF475569))),
                      ),
                      const SizedBox(width: 6),
                      Text(ticket.createdAtHuman ?? '', style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8))),
                    ],
                  ),
                  OutlinedButton.icon(
                    onPressed: () => _showStatusDialog(ticket),
                    icon: const Icon(Icons.edit_note_rounded, size: 16),
                    label: const Text('Status', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      visualDensity: VisualDensity.compact,
                      foregroundColor: const Color(0xFF4F46E5),
                      side: const BorderSide(color: Color(0xFF818CF8)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
