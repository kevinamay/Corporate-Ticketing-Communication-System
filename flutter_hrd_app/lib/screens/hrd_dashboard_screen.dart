import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class HrdDashboardScreen extends StatefulWidget {
  final Function(int) onNavigateToTab;
  const HrdDashboardScreen({super.key, required this.onNavigateToTab});

  @override
  State<HrdDashboardScreen> createState() => _HrdDashboardScreenState();
}

class _HrdDashboardScreenState extends State<HrdDashboardScreen> {
  UserModel? _user;
  Map<String, dynamic> _metrics = {};
  List<Map<String, dynamic>> _dailyTrend = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    final user = await ApiService.getSavedUser();
    final metrics = await ApiService.getHrdMetrics();
    final chart = await ApiService.getDailyChart();

    if (!mounted) return;

    setState(() {
      _user = user;
      _metrics = metrics;
      _dailyTrend = chart;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _loadDashboardData,
          color: const Color(0xFF4F46E5),
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Top Executive Profile Header
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF0F172A), Color(0xFF312E81)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF312E81).withValues(alpha: 0.3),
                        blurRadius: 16,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 26,
                        backgroundColor: const Color(0xFF4F46E5),
                        backgroundImage: _user?.avatar != null ? NetworkImage(_user!.avatar!) : null,
                        child: _user?.avatar == null
                            ? const Icon(Icons.admin_panel_settings, color: Colors.white, size: 28)
                            : null,
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF6366F1).withValues(alpha: 0.3),
                                    borderRadius: BorderRadius.circular(10),
                                    border: Border.all(color: const Color(0xFF818CF8).withValues(alpha: 0.4)),
                                  ),
                                  child: Text(
                                    _user?.role == 'admin' ? 'ADMINISTRATOR IT' : 'HUMAN RESOURCES (HRD)',
                                    style: const TextStyle(
                                      color: Color(0xFFC7D2FE),
                                      fontSize: 9,
                                      fontWeight: FontWeight.bold,
                                      letterSpacing: 0.6,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 4),
                            Text(
                              _user?.name ?? 'Staf HRD / Admin',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.w900,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 2),
                            Text(
                              _user?.email ?? '',
                              style: const TextStyle(
                                color: Color(0xFF94A3B8),
                                fontSize: 11,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 18),

                // Pending Approval Alert if any
                if ((_metrics['pending_approvals'] ?? 0) > 0) ...[
                  GestureDetector(
                    onTap: () => widget.onNavigateToTab(2), // Jump to HCM tab
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFFBEB),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: const Color(0xFFFDE68A)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.pending_actions_rounded, color: Color(0xFFD97706), size: 22),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Text(
                              'Ada ${_metrics['pending_approvals']} pendaftaran karyawan baru menunggu ACC!',
                              style: const TextStyle(
                                color: Color(0xFF92400E),
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Color(0xFFD97706)),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                ],

                // Elevated KPI Grid
                const Text(
                  'Key Performance Indicators (KPI)',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                ),
                const SizedBox(height: 12),

                Row(
                  children: [
                    _kpiBox('Tiket Masuk', '${_metrics['total_tickets'] ?? 0}', Icons.inbox_outlined, const Color(0xFF4F46E5), const Color(0xFFEEF2FF)),
                    const SizedBox(width: 10),
                    _kpiBox('Perlu Tindakan', '${_metrics['pending_tickets'] ?? 0}', Icons.warning_amber_rounded, const Color(0xFFF59E0B), const Color(0xFFFEF3C7)),
                  ],
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    _kpiBox('Sedang Dikerjakan', '${_metrics['in_progress_tickets'] ?? 0}', Icons.sync_rounded, const Color(0xFF0284C7), const Color(0xFFE0F2FE)),
                    const SizedBox(width: 10),
                    _kpiBox('Tiket Selesai', '${_metrics['resolved_tickets'] ?? 0}', Icons.check_circle_rounded, const Color(0xFF10B981), const Color(0xFFECFDF5)),
                  ],
                ),
                const SizedBox(height: 22),

                // Visual Chart Card: Tren Permintaan Tiket Harian
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 10, offset: const Offset(0, 4)),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Row(
                            children: [
                              Icon(Icons.bar_chart_rounded, size: 20, color: Color(0xFF4F46E5)),
                              SizedBox(width: 8),
                              Text(
                                'Tren Permintaan Tiket Harian',
                                style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(color: const Color(0xFFEEF2FF), borderRadius: BorderRadius.circular(8)),
                            child: const Text('7 Hari Terakhir', style: TextStyle(fontSize: 10, color: Color(0xFF4338CA), fontWeight: FontWeight.bold)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),

                      if (_isLoading)
                        const SizedBox(height: 120, child: Center(child: CircularProgressIndicator()))
                      else if (_dailyTrend.isEmpty)
                        const SizedBox(height: 100, child: Center(child: Text('Data tren belum tersedia', style: TextStyle(fontSize: 12, color: Colors.grey))))
                      else
                        _buildBarChart(),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Action Shortcuts
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => widget.onNavigateToTab(1),
                        icon: const Icon(Icons.forum_outlined, size: 18),
                        label: const Text('Tangani Tiket', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF4F46E5),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: () => widget.onNavigateToTab(2),
                        icon: const Icon(Icons.people_outline_rounded, size: 18),
                        label: const Text('Data Karyawan', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: const Color(0xFF4F46E5),
                          side: const BorderSide(color: Color(0xFF4F46E5)),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _kpiBox(String label, String value, IconData icon, Color color, Color bg) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 4, offset: const Offset(0, 2)),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                  Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: Color(0xFF64748B)), maxLines: 1, overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBarChart() {
    int maxCount = 1;
    for (var day in _dailyTrend) {
      final c = (day['count'] ?? 0) as int;
      if (c > maxCount) maxCount = c;
    }

    return SizedBox(
      height: 140,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: _dailyTrend.map((day) {
          final count = (day['count'] ?? 0) as int;
          final double ratio = count / maxCount;
          final double barHeight = (ratio * 80).clamp(6.0, 80.0);

          return Column(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Text(
                '$count',
                style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF475569)),
              ),
              const SizedBox(height: 4),
              Container(
                width: 22,
                height: barHeight,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: count > 0
                        ? [const Color(0xFF6366F1), const Color(0xFF4F46E5)]
                        : [const Color(0xFFCBD5E1), const Color(0xFFE2E8F0)],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                  borderRadius: BorderRadius.circular(6),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                day['short_label'] ?? '',
                style: const TextStyle(fontSize: 9, color: Color(0xFF94A3B8), fontWeight: FontWeight.w600),
              ),
            ],
          );
        }).toList(),
      ),
    );
  }
}
