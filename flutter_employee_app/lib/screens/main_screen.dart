import 'package:flutter/material.dart';
import 'create_ticket_screen.dart';
import 'home_screen.dart';
import 'profile_screen.dart';
import 'ticket_list_screen.dart';

class MainScreen extends StatefulWidget {
  final int initialIndex;
  const MainScreen({super.key, this.initialIndex = 0});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  late int _currentIndex;
  int _currentVariant = 0; // 0=A, 1=B, 2=C, 3=D, 4=E, 5=F
  bool _isSwitcherExpanded = true;

  final List<String> _variantLabels = [
    'A: Feed Korporat',
    'B: Executive Island',
    'C: Cyber Slate',
    'D: Center Action FAB',
    'E: Glass Luxe',
    'F: Factory Pro',
  ];

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
  }

  void switchTab(int index) {
    setState(() {
      _currentIndex = index;
    });
  }

  void setVariant(int index) {
    setState(() {
      _currentVariant = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final screens = [
      HomeScreen(
        onNavigateToTab: switchTab,
        prototypeVariant: _currentVariant,
        onVariantChanged: (v) => setState(() => _currentVariant = v),
      ),
      CreateTicketScreen(onTicketCreated: () => switchTab(2)),
      const TicketListScreen(),
      const ProfileScreen(),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      body: SafeArea(
        child: Column(
          children: [
            // ================================================================
            // TOP PROTOTYPE SWITCHER BAR (Does NOT block the bottom navigation)
            // ================================================================
            _buildTopPrototypeBar(),

            // Screen Content
            Expanded(
              child: IndexedStack(
                index: _currentIndex,
                children: screens,
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: _buildThemedBottomNav(),
    );
  }

  // ==========================================================================
  // TOP PROTOTYPE CONTROL BAR (A to F)
  // ==========================================================================
  Widget _buildTopPrototypeBar() {
    if (!_isSwitcherExpanded) {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
        color: const Color(0xFF0F172A),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.style_rounded, color: Color(0xFF60A5FA), size: 14),
                const SizedBox(width: 6),
                Text(
                  'PROTOPTIPE AKTIF: DESAIN ${String.fromCharCode(65 + _currentVariant)}',
                  style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w900),
                ),
              ],
            ),
            InkWell(
              onTap: () => setState(() => _isSwitcherExpanded = true),
              borderRadius: BorderRadius.circular(12),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF2563EB),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Row(
                  children: [
                    Text('Ubah Desain', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)),
                    SizedBox(width: 2),
                    Icon(Icons.keyboard_arrow_down_rounded, color: Colors.white, size: 14),
                  ],
                ),
              ),
            ),
          ],
        ),
      );
    }

    return Container(
      padding: const EdgeInsets.fromLTRB(10, 6, 10, 6),
      decoration: const BoxDecoration(
        color: Color(0xFF0B132B),
        border: Border(bottom: BorderSide(color: Color(0xFF1E293B))),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFF2563EB),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: const Text('PROTOTYPE', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900)),
                  ),
                  const SizedBox(width: 6),
                  Text(
                    'PILIH GAYA: ${_variantLabels[_currentVariant]}',
                    style: const TextStyle(color: Color(0xFF93C5FD), fontSize: 11, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              InkWell(
                onTap: () => setState(() => _isSwitcherExpanded = false),
                borderRadius: BorderRadius.circular(12),
                child: const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                  child: Row(
                    children: [
                      Text('Sembunyikan', style: TextStyle(color: Color(0xFF94A3B8), fontSize: 9)),
                      Icon(Icons.keyboard_arrow_up_rounded, color: Color(0xFF94A3B8), size: 14),
                    ],
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),

          // Chips A to F
          Row(
            children: List.generate(6, (index) {
              final letter = String.fromCharCode(65 + index);
              final isSelected = _currentVariant == index;
              return Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 2),
                  child: InkWell(
                    onTap: () => setVariant(index),
                    borderRadius: BorderRadius.circular(8),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 5),
                      decoration: BoxDecoration(
                        color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF1E293B),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: isSelected ? const Color(0xFF60A5FA) : const Color(0xFF334155),
                          width: isSelected ? 1.5 : 1,
                        ),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        'DESAIN $letter',
                        style: TextStyle(
                          color: isSelected ? Colors.white : const Color(0xFFCBD5E1),
                          fontSize: 10,
                          fontWeight: isSelected ? FontWeight.w900 : FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ),
              );
            }),
          ),
        ],
      ),
    );
  }

  // ==========================================================================
  // 6 THEMED BOTTOM NAVIGATION BAR STYLES (A TO F)
  // ==========================================================================
  Widget _buildThemedBottomNav() {
    switch (_currentVariant) {
      case 0:
        return _buildBottomNavA();
      case 1:
        return _buildBottomNavB();
      case 2:
        return _buildBottomNavC();
      case 3:
        return _buildBottomNavD();
      case 4:
        return _buildBottomNavE();
      case 5:
      default:
        return _buildBottomNavF();
    }
  }

  // --------------------------------------------------------------------------
  // STYLE A: Corporate Precision Flat (White, blue pill highlight on active)
  // --------------------------------------------------------------------------
  Widget _buildBottomNavA() {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
      ),
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItemA(0, Icons.dashboard_rounded, 'Beranda'),
          _navItemA(1, Icons.add_circle_outline_rounded, 'Buat Tiket'),
          _navItemA(2, Icons.list_alt_rounded, 'Antrean Tiket'),
          _navItemA(3, Icons.person_rounded, 'Profil'),
        ],
      ),
    );
  }

  Widget _navItemA(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      borderRadius: BorderRadius.circular(12),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFFEFF6FF) : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
              size: 20,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --------------------------------------------------------------------------
  // STYLE B: Floating Rounded Island Dock (iOS / Material 3 floating pill)
  // --------------------------------------------------------------------------
  Widget _buildBottomNavB() {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 0, 16, 12),
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 18,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItemB(0, Icons.home_rounded, 'Beranda'),
          _navItemB(1, Icons.edit_note_rounded, 'Buat Tiket'),
          _navItemB(2, Icons.inbox_rounded, 'Antrean'),
          _navItemB(3, Icons.person_outline_rounded, 'Profil'),
        ],
      ),
    );
  }

  Widget _navItemB(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      borderRadius: BorderRadius.circular(20),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 250),
        padding: EdgeInsets.symmetric(horizontal: isSelected ? 14 : 10, vertical: 6),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2563EB) : Colors.transparent,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected ? Colors.white : const Color(0xFF64748B),
              size: 18,
            ),
            if (isSelected) ...[
              const SizedBox(width: 6),
              Text(
                label,
                style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  // --------------------------------------------------------------------------
  // STYLE C: Cyber-Slate Dark Dock (Dark #0F172A with neon green/cyan glow dot)
  // --------------------------------------------------------------------------
  Widget _buildBottomNavC() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8),
      decoration: const BoxDecoration(
        color: Color(0xFF0F172A),
        border: Border(top: BorderSide(color: Color(0xFF1E293B))),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItemC(0, Icons.grid_view_rounded, 'Beranda'),
          _navItemC(1, Icons.add_box_rounded, 'Buat Tiket'),
          _navItemC(2, Icons.layers_rounded, 'Antrean'),
          _navItemC(3, Icons.account_circle_rounded, 'Profil'),
        ],
      ),
    );
  }

  Widget _navItemC(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            icon,
            color: isSelected ? const Color(0xFF38BDF8) : const Color(0xFF64748B),
            size: 20,
          ),
          const SizedBox(height: 3),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              fontWeight: isSelected ? FontWeight.w900 : FontWeight.w500,
              color: isSelected ? const Color(0xFF38BDF8) : const Color(0xFF94A3B8),
            ),
          ),
          const SizedBox(height: 3),
          Container(
            width: 4,
            height: 4,
            decoration: BoxDecoration(
              color: isSelected ? const Color(0xFF38BDF8) : Colors.transparent,
              shape: BoxShape.circle,
            ),
          ),
        ],
      ),
    );
  }

  // --------------------------------------------------------------------------
  // STYLE D: Prominent Center Action FAB Bar (Notched Center + Button)
  // --------------------------------------------------------------------------
  Widget _buildBottomNavD() {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          // Tab 0: Beranda
          _navItemD(0, Icons.home_rounded, 'Beranda'),
          // Tab 2: Antrean
          _navItemD(2, Icons.assignment_rounded, 'Antrean'),

          // Center Prominent Action Button: + Buat Tiket
          InkWell(
            onTap: () => switchTab(1),
            borderRadius: BorderRadius.circular(30),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF2563EB), Color(0xFF1D4ED8)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF2563EB).withValues(alpha: 0.35),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.add, color: Colors.white, size: 16),
                  SizedBox(width: 4),
                  Text('Buat Tiket', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w900)),
                ],
              ),
            ),
          ),

          // Tab 3: Riwayat Tiket / Status
          _navItemD(1, Icons.history_rounded, 'Histori'),
          // Tab 4: Profil
          _navItemD(3, Icons.person_rounded, 'Profil'),
        ],
      ),
    );
  }

  Widget _navItemD(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
              size: 20,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --------------------------------------------------------------------------
  // STYLE E: Minimalist Frosted Luxe Glass Pill
  // --------------------------------------------------------------------------
  Widget _buildBottomNavE() {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 0, 20, 10),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.92),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFCBD5E1).withValues(alpha: 0.6)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItemE(0, Icons.circle_outlined, Icons.lens_rounded, 'Beranda'),
          _navItemE(1, Icons.create_outlined, Icons.create_rounded, 'Ajukan'),
          _navItemE(2, Icons.format_list_bulleted_outlined, Icons.format_list_bulleted_rounded, 'Antrean'),
          _navItemE(3, Icons.person_outline_rounded, Icons.person_rounded, 'Akun'),
        ],
      ),
    );
  }

  Widget _navItemE(int index, IconData unselectedIcon, IconData selectedIcon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            isSelected ? selectedIcon : unselectedIcon,
            color: isSelected ? const Color(0xFF0F172A) : const Color(0xFF94A3B8),
            size: 19,
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              fontSize: 9,
              letterSpacing: 0.5,
              fontWeight: isSelected ? FontWeight.w900 : FontWeight.w500,
              color: isSelected ? const Color(0xFF0F172A) : const Color(0xFF94A3B8),
            ),
          ),
        ],
      ),
    );
  }

  // --------------------------------------------------------------------------
  // STYLE F: Asia Plastik High-Contrast Factory Pro Strip
  // --------------------------------------------------------------------------
  Widget _buildBottomNavF() {
    return Container(
      decoration: const BoxDecoration(
        color: Color(0xFF081021),
        border: Border(top: BorderSide(color: Color(0xFFF59E0B), width: 2)),
      ),
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItemF(0, Icons.space_dashboard_rounded, 'BERANDA'),
          _navItemF(1, Icons.post_add_rounded, 'BUAT TIKET'),
          _navItemF(2, Icons.confirmation_number_rounded, 'ANTREAN'),
          _navItemF(3, Icons.badge_rounded, 'PROFIL'),
        ],
      ),
    );
  }

  Widget _navItemF(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return InkWell(
      onTap: () => switchTab(index),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFFF59E0B).withValues(alpha: 0.15) : Colors.transparent,
          borderRadius: BorderRadius.circular(6),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected ? const Color(0xFFF59E0B) : const Color(0xFF64748B),
              size: 20,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 9,
                fontWeight: FontWeight.w900,
                letterSpacing: 0.5,
                color: isSelected ? const Color(0xFFF59E0B) : const Color(0xFF64748B),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
