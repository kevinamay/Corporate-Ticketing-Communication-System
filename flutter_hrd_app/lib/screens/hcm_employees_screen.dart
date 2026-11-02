import 'package:flutter/material.dart';
import '../models/employee_model.dart';
import '../services/api_service.dart';

class HcmEmployeesScreen extends StatefulWidget {
  const HcmEmployeesScreen({super.key});

  @override
  State<HcmEmployeesScreen> createState() => _HcmEmployeesScreenState();
}

class _HcmEmployeesScreenState extends State<HcmEmployeesScreen> {
  final _searchController = TextEditingController();
  String _filterStatus = 'all'; // all, pending, approved
  List<EmployeeModel> _employees = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadEmployees();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadEmployees() async {
    setState(() => _isLoading = true);
    final res = await ApiService.getEmployees(
      search: _searchController.text.trim().isEmpty ? null : _searchController.text.trim(),
      approvalStatus: _filterStatus,
    );

    if (!mounted) return;

    setState(() {
      _employees = res;
      _isLoading = false;
    });
  }

  Future<void> _toggleApproval(EmployeeModel emp) async {
    final res = await ApiService.toggleEmployeeApproval(emp.id);
    if (!mounted) return;

    if (res['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Status berhasil diubah'),
          backgroundColor: res['is_approved'] == true ? const Color(0xFF10B981) : const Color(0xFFF59E0B),
        ),
      );
      setState(() {
        final index = _employees.indexWhere((e) => e.id == emp.id);
        if (index != -1) {
          _employees[index] = _employees[index].copyWith(isApproved: res['is_approved']);
        }
      });
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(res['message'] ?? 'Gagal mengubah status')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text(
          'Master Data Karyawan (HCM)',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: false,
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Search & Filter Header
            Container(
              color: Colors.white,
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
              child: Column(
                children: [
                  TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari nama, email, NIK KTP...',
                      hintStyle: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                      prefixIcon: const Icon(Icons.search_rounded, size: 20, color: Color(0xFF64748B)),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                _loadEmployees();
                              },
                            )
                          : null,
                      filled: true,
                      fillColor: const Color(0xFFF1F5F9),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    ),
                    onSubmitted: (_) => _loadEmployees(),
                  ),
                  const SizedBox(height: 8),

                  Row(
                    children: [
                      _filterChip('Semua Karyawan', 'all'),
                      const SizedBox(width: 6),
                      _filterChip('Menunggu ACC', 'pending'),
                      const SizedBox(width: 6),
                      _filterChip('Disetujui', 'approved'),
                    ],
                  ),
                ],
              ),
            ),

            // Employee List
            Expanded(
              child: RefreshIndicator(
                onRefresh: _loadEmployees,
                color: const Color(0xFF4F46E5),
                child: _isLoading
                    ? const Center(child: CircularProgressIndicator())
                    : _employees.isEmpty
                        ? Center(
                            child: SingleChildScrollView(
                              physics: const AlwaysScrollableScrollPhysics(),
                              padding: const EdgeInsets.all(24),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.person_off_outlined, size: 48, color: Colors.grey.shade400),
                                  const SizedBox(height: 12),
                                  const Text('Tidak Ada Data Karyawan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF334155))),
                                  const SizedBox(height: 4),
                                  const Text('Tidak ada akun yang sesuai kriteria pencarian.', style: TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
                                ],
                              ),
                            ),
                          )
                        : ListView.builder(
                            padding: const EdgeInsets.all(16),
                            itemCount: _employees.length,
                            itemBuilder: (ctx, index) => _employeeCard(_employees[index]),
                          ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _filterChip(String label, String value) {
    final isSelected = _filterStatus == value;
    return ChoiceChip(
      label: Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: isSelected ? Colors.white : const Color(0xFF475569))),
      selected: isSelected,
      selectedColor: const Color(0xFF4F46E5),
      backgroundColor: const Color(0xFFF1F5F9),
      side: BorderSide.none,
      onSelected: (val) {
        if (val) {
          setState(() => _filterStatus = value);
          _loadEmployees();
        }
      },
    );
  }

  Widget _employeeCard(EmployeeModel emp) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 4, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header: Avatar, Name, & Approval Status Pill
          Row(
            children: [
              CircleAvatar(
                radius: 20,
                backgroundColor: const Color(0xFF4F46E5),
                backgroundImage: emp.avatar != null ? NetworkImage(emp.avatar!) : null,
                child: emp.avatar == null ? const Icon(Icons.person, color: Colors.white) : null,
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      emp.name,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    Text(
                      emp.email,
                      style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: emp.isApproved ? const Color(0xFFECFDF5) : const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  emp.isApproved ? 'Aktif (Disetujui)' : 'Menunggu ACC',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    color: emp.isApproved ? const Color(0xFF047857) : const Color(0xFFB45309),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Details Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6)),
                    child: Text(emp.departmentName, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF475569))),
                  ),
                  if (emp.whatsappNumber != null) ...[
                    const SizedBox(width: 8),
                    Text(emp.whatsappNumber!, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                  ],
                ],
              ),

              // Action button to toggle approval
              ElevatedButton.icon(
                onPressed: () => _toggleApproval(emp),
                icon: Icon(emp.isApproved ? Icons.block_rounded : Icons.check_circle_outline_rounded, size: 14),
                label: Text(emp.isApproved ? 'Nonaktifkan' : 'ACC Akun', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: emp.isApproved ? const Color(0xFFFEE2E2) : const Color(0xFF10B981),
                  foregroundColor: emp.isApproved ? const Color(0xFFDC2626) : Colors.white,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  visualDensity: VisualDensity.compact,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
