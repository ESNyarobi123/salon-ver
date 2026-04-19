import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../widgets/order_card.dart';
import '../widgets/stat_chip.dart';
import 'login_screen.dart';
import 'new_order_screen.dart';
import 'order_detail_screen.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen>
    with TickerProviderStateMixin {
  final ApiService _api = ApiService();
  OrdersData? _data;
  AuthData? _authData;
  bool _isLoading = true;
  String? _error;
  int _selectedTab = 0;
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController =
        TabController(length: SalonStrings.bookingTabs.length, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() => _selectedTab = _tabController.index);
      }
    });
    _loadData();
    _loadAuthData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadAuthData() async {
    final auth = await _api.getAuthData();
    if (mounted) setState(() => _authData = auth);
  }

  Future<void> _loadData({bool showLoading = true}) async {
    if (showLoading) setState(() => _isLoading = true);
    try {
      final data = await _api.getOrders();
      if (mounted) {
        setState(() {
          _data = data;
          _isLoading = false;
          _error = null;
        });
      }
    } on UnauthorizedException {
      if (mounted) _redirectToLogin();
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e.toString();
          _isLoading = false;
        });
      }
    }
  }

  void _redirectToLogin() {
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (_) => false,
    );
  }

  Future<void> _logout() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        backgroundColor: AppTheme.surface,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(SalonStrings.logoutTitle,
            style: GoogleFonts.poppins(
                color: AppTheme.textPrimary, fontWeight: FontWeight.w600)),
        content: Text(SalonStrings.logoutBody,
            style: GoogleFonts.poppins(color: AppTheme.textSecondary)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(SalonStrings.dialogNo),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            onPressed: () => Navigator.pop(context, true),
            child: const Text(SalonStrings.dialogLogout),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      await _api.logout();
      if (mounted) _redirectToLogin();
    }
  }

  Future<void> _updateStatus(Order order, String newStatus) async {
    HapticFeedback.lightImpact();
    try {
      await _api.updateOrderStatus(order.id, newStatus);
      _showSnack(
        SalonStrings.snackBookingStatusUpdated(order.id, newStatus),
        AppTheme.success,
      );
      await _loadData(showLoading: false);
    } catch (e) {
      _showSnack(e.toString(), AppTheme.error);
    }
  }

  Future<void> _deleteOrder(Order order) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        backgroundColor: AppTheme.surface,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(SalonStrings.deleteBookingTitle(order.id),
            style: GoogleFonts.poppins(
                color: AppTheme.textPrimary, fontWeight: FontWeight.w600)),
        content: Text(
          SalonStrings.deleteBookingBodyConfirm,
          style: GoogleFonts.poppins(color: AppTheme.textSecondary),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text(SalonStrings.dialogNo),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            onPressed: () => Navigator.pop(context, true),
            child: const Text(SalonStrings.dialogDelete),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        await _api.deleteOrder(order.id);
        _showSnack(SalonStrings.snackBookingDeleted(order.id), AppTheme.success);
        await _loadData(showLoading: false);
      } catch (e) {
        _showSnack(e.toString(), AppTheme.error);
      }
    }
  }

  void _showSnack(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color.withOpacity(0.9),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        margin: const EdgeInsets.all(16),
      ),
    );
  }

  void _openOrderDetail(Order order) {
    Navigator.of(context).push(
      PageRouteBuilder(
        pageBuilder: (_, __, ___) => OrderDetailScreen(
          order: order,
          menuItems: _data?.menuItems ?? [],
          onUpdate: () => _loadData(showLoading: false),
        ),
        transitionsBuilder: (_, anim, __, child) => SlideTransition(
          position: Tween(
            begin: const Offset(1.0, 0.0),
            end: Offset.zero,
          ).animate(CurvedAnimation(parent: anim, curve: Curves.easeOutCubic)),
          child: child,
        ),
        transitionDuration: const Duration(milliseconds: 350),
      ),
    );
  }

  void _openNewOrder() {
    if (_data == null) return;
    Navigator.of(context).push(
      PageRouteBuilder(
        pageBuilder: (_, __, ___) => NewOrderScreen(
          tables: _data!.tables,
          bookingCategories: _data!.bookingCategories,
          onCreated: () => _loadData(showLoading: false),
        ),
        transitionsBuilder: (_, anim, __, child) => SlideTransition(
          position: Tween(
            begin: const Offset(0.0, 1.0),
            end: Offset.zero,
          ).animate(CurvedAnimation(parent: anim, curve: Curves.easeOutCubic)),
          child: child,
        ),
        transitionDuration: const Duration(milliseconds: 350),
      ),
    );
  }

  List<Order> get _currentOrders {
    if (_data == null) return [];
    switch (_selectedTab) {
      case 0:
        return _data!.pending;
      case 1:
        return _data!.preparing;
      case 2:
        return _data!.served;
      case 3:
        return _data!.paid;
      default:
        return [];
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isTablet = size.width > 600;

    return Scaffold(
      backgroundColor: AppTheme.bg,
      body: NestedScrollView(
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          SliverAppBar(
            expandedHeight: isTablet ? 160 : 140,
            floating: true,
            pinned: true,
            backgroundColor: AppTheme.bg,
            elevation: 0,
            automaticallyImplyLeading: false,
            flexibleSpace: FlexibleSpaceBar(
              background: _buildHeader(isTablet),
            ),
            bottom: PreferredSize(
              preferredSize: const Size.fromHeight(56),
              child: _buildTabBar(),
            ),
          ),
        ],
        body: _buildBody(isTablet),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _data != null ? _openNewOrder : null,
        backgroundColor: AppTheme.primary,
        foregroundColor: Colors.white,
        elevation: 4,
        icon: const Icon(Icons.add_rounded),
        label: Text(
          SalonStrings.fabNewBooking,
          style: GoogleFonts.poppins(fontWeight: FontWeight.w600),
        ),
      )
          .animate()
          .scale(delay: 800.ms, duration: 400.ms, curve: Curves.elasticOut),
    );
  }

  Widget _buildHeader(bool isTablet) {
    return Container(
      decoration: const BoxDecoration(
        color: AppTheme.bg,
        border: Border(bottom: BorderSide(color: AppTheme.border)),
      ),
      padding: EdgeInsets.fromLTRB(
        isTablet ? 32 : 20,
        MediaQuery.of(context).padding.top + 12,
        isTablet ? 32 : 20,
        12,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: AppTheme.surface,
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: Colors.white.withValues(alpha: 0.3),
                    width: 2,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.primary.withValues(alpha: 0.15),
                      blurRadius: 8,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: ClipOval(
                  child: Image.asset(
                    'assets/images/logo.jpeg',
                    fit: BoxFit.cover,
                  ),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _authData?.restaurantName ?? SalonStrings.appTitle,
                      style: GoogleFonts.poppins(
                        fontSize: isTablet ? 22 : 18,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        letterSpacing: -0.5,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 2),
                    Row(
                      children: [
                        const Icon(Icons.person_pin_circle_rounded,
                            size: 14, color: AppTheme.primary),
                        const SizedBox(width: 4),
                        Flexible(
                          child: Text(
                            _authData != null
                                ? SalonStrings.headerStylistLine(
                                    _authData!.userName,
                                  )
                                : SalonStrings.headerServiceDeskLine,
                            style: GoogleFonts.poppins(
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                              color: AppTheme.textSecondary,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              PopupMenuButton<String>(
                color: AppTheme.surfaceVariant,
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14)),
                offset: const Offset(0, 40),
                icon: const Icon(Icons.more_vert_rounded,
                    color: AppTheme.textSecondary),
                onSelected: (value) {
                  HapticFeedback.lightImpact();
                  if (value == 'refresh') {
                    _loadData();
                  } else if (value == 'logout') {
                    _logout();
                  }
                },
                itemBuilder: (context) => [
                  PopupMenuItem(
                    value: 'refresh',
                    child: Row(
                      children: [
                        const Icon(Icons.refresh_rounded,
                            color: AppTheme.primary, size: 20),
                        const SizedBox(width: 10),
                        Text(SalonStrings.menuRefreshData,
                            style: GoogleFonts.poppins(
                                color: AppTheme.textPrimary, fontSize: 13)),
                      ],
                    ),
                  ),
                  const PopupMenuDivider(),
                  PopupMenuItem(
                    value: 'logout',
                    child: Row(
                      children: [
                        const Icon(Icons.logout_rounded,
                            color: AppTheme.error, size: 20),
                        const SizedBox(width: 10),
                        Text(SalonStrings.menuLogout,
                            style: GoogleFonts.poppins(
                                color: AppTheme.error, fontSize: 13)),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
          if (_data != null) ...[
            const SizedBox(height: 16),
            _buildStatsRow(),
          ],
        ],
      ),
    );
  }

  Widget _buildStatsRow() {
    final currency = NumberFormat('#,##0', 'en_US');
    final totalRevenue =
        _data!.paid.fold<double>(0, (s, o) => s + o.totalAmount);
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: [
          StatChip(
            count: _data!.pending.length,
            color: AppTheme.statusPending,
            icon: Icons.hourglass_empty_rounded,
          ),
          const SizedBox(width: 8),
          StatChip(
            count: _data!.preparing.length,
            color: AppTheme.statusPreparing,
            icon: Icons.restaurant_rounded,
          ),
          const SizedBox(width: 12),
          Container(width: 1, height: 24, color: AppTheme.border),
          const SizedBox(width: 12),
          _buildStatItem(
            SalonStrings.activeBookingsCount(_data!.totalActive),
            Icons.pending_actions_rounded,
            AppTheme.primary,
          ),
          const SizedBox(width: 16),
          _buildStatItem(
            'TZS ${currency.format(totalRevenue)}',
            Icons.payments_rounded,
            AppTheme.success,
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem(String label, IconData icon, Color color) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, color: color, size: 14),
        const SizedBox(width: 4),
        Text(
          label,
          style: GoogleFonts.poppins(
            fontSize: 11,
            color: AppTheme.textSecondary,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildTabBar() {
    final tabColors = [
      AppTheme.statusPending,
      AppTheme.statusPreparing,
      AppTheme.statusServed,
      AppTheme.statusPaid,
    ];
    final counts = _data == null
        ? [0, 0, 0, 0]
        : [
            _data!.pending.length,
            _data!.preparing.length,
            _data!.served.length,
            _data!.paid.length,
          ];

    return Container(
      color: AppTheme.bg,
      child: TabBar(
        controller: _tabController,
        isScrollable: true,
        tabAlignment: TabAlignment.start,
        indicatorColor: tabColors[_selectedTab],
        indicatorWeight: 3,
        dividerColor: AppTheme.border,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        tabs: List.generate(
          SalonStrings.bookingTabs.length,
          (i) => Tab(
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  SalonStrings.bookingTabs[i],
                  style: GoogleFonts.poppins(
                    fontWeight:
                        _selectedTab == i ? FontWeight.w600 : FontWeight.w400,
                    color: _selectedTab == i
                        ? tabColors[i]
                        : AppTheme.textSecondary,
                    fontSize: 13,
                  ),
                ),
                if (counts[i] > 0) ...[
                  const SizedBox(width: 6),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                    decoration: BoxDecoration(
                      color: tabColors[i].withOpacity(0.2),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      '${counts[i]}',
                      style: GoogleFonts.poppins(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: tabColors[i],
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildBody(bool isTablet) {
    if (_isLoading) return _buildSkeleton(isTablet);
    if (_error != null) return _buildError();

    final orders = _currentOrders;
    if (orders.isEmpty) return _buildEmpty();

    if (isTablet) {
      return GridView.builder(
        padding: const EdgeInsets.all(20),
        gridDelegate: const SliverGridDelegateWithMaxCrossAxisExtent(
          maxCrossAxisExtent: 420,
          mainAxisSpacing: 16,
          crossAxisSpacing: 16,
          mainAxisExtent: 260,
        ),
        itemCount: orders.length,
        itemBuilder: (_, i) => OrderCard(
          order: orders[i],
          onTap: () => _openOrderDetail(orders[i]),
          onUpdateStatus: (s) => _updateStatus(orders[i], s),
          onDelete: () => _deleteOrder(orders[i]),
        )
            .animate()
            .fadeIn(delay: (i * 60).ms, duration: 400.ms)
            .slideY(begin: 0.15),
      );
    }

    return RefreshIndicator(
      color: AppTheme.primary,
      backgroundColor: AppTheme.surface,
      onRefresh: () => _loadData(showLoading: false),
      child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        itemCount: orders.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (_, i) => OrderCard(
          order: orders[i],
          onTap: () => _openOrderDetail(orders[i]),
          onUpdateStatus: (s) => _updateStatus(orders[i], s),
          onDelete: () => _deleteOrder(orders[i]),
        )
            .animate()
            .fadeIn(delay: (i * 60).ms, duration: 400.ms)
            .slideY(begin: 0.15),
      ),
    );
  }

  Widget _buildSkeleton(bool isTablet) {
    if (isTablet) {
      return GridView.builder(
        padding: const EdgeInsets.all(20),
        gridDelegate: const SliverGridDelegateWithMaxCrossAxisExtent(
          maxCrossAxisExtent: 420,
          mainAxisSpacing: 16,
          crossAxisSpacing: 16,
          mainAxisExtent: 260,
        ),
        itemCount: 6,
        itemBuilder: (_, i) => _buildSkeletonCard()
            .animate(delay: (i * 60).ms)
            .fadeIn(duration: 400.ms),
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: 4,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (_, i) => _buildSkeletonCard()
          .animate(delay: (i * 60).ms)
          .fadeIn(duration: 400.ms),
    );
  }

  Widget _buildSkeletonCard() {
    return Container(
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          // Header
          Container(
            padding: const EdgeInsets.fromLTRB(14, 12, 10, 10),
            decoration: const BoxDecoration(
              border: Border(bottom: BorderSide(color: AppTheme.border)),
            ),
            child: Row(
              children: [
                Container(
                  width: 30,
                  height: 30,
                  decoration: BoxDecoration(
                    color: AppTheme.surfaceVariant,
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                          width: 100,
                          height: 16,
                          color: AppTheme.surfaceVariant),
                      const SizedBox(height: 6),
                      Container(
                          width: 140,
                          height: 12,
                          color: AppTheme.surfaceVariant),
                    ],
                  ),
                ),
                Container(
                    width: 40, height: 12, color: AppTheme.surfaceVariant),
              ],
            ),
          ),

          // Items
          Padding(
            padding: const EdgeInsets.fromLTRB(14, 12, 14, 12),
            child: Column(
              children: List.generate(
                  3,
                  (i) => Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: Row(
                          children: [
                            Container(
                                width: 16,
                                height: 12,
                                color: AppTheme.surfaceVariant),
                            const SizedBox(width: 8),
                            Expanded(
                                child: Container(
                                    height: 12,
                                    color: AppTheme.surfaceVariant)),
                            const SizedBox(width: 16),
                            Container(
                                width: 60,
                                height: 12,
                                color: AppTheme.surfaceVariant),
                          ],
                        ),
                      )),
            ),
          ),

          const SizedBox(height: 16),

          // Footer
          Container(
            padding: const EdgeInsets.fromLTRB(14, 12, 14, 12),
            decoration: const BoxDecoration(
              border: Border(top: BorderSide(color: AppTheme.border)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                    width: 60, height: 14, color: AppTheme.surfaceVariant),
                Container(
                    width: 80, height: 18, color: AppTheme.surfaceVariant),
              ],
            ),
          ),
        ],
      ).animate(onPlay: (c) => c.repeat()).shimmer(
          duration: 1500.ms, color: AppTheme.border.withValues(alpha: 0.5)),
    );
  }

  Widget _buildError() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppTheme.error.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.wifi_off_rounded,
                  color: AppTheme.error, size: 48),
            ),
            const SizedBox(height: 20),
            Text(
              SalonStrings.connectionErrorTitle,
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: AppTheme.textPrimary,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              _error!,
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(
                fontSize: 13,
                color: AppTheme.textSecondary,
              ),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: _loadData,
              icon: const Icon(Icons.refresh_rounded),
              label: const Text(SalonStrings.tryAgain),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmpty() {
    final emptyConfig = {
      0: ('🕐', 'Hakuna bookings zinasubiri', 'Hakuna booking inayosubiri kwa sasa.'),
      1: ('💇', 'Hakuna zinazoendelea', 'Hakuna booking inayoendelea (huduma).'),
      2: ('✨', 'Hakuna zilizo tayari', 'Hakuna booking iliyokamilika bila malipo bado.'),
      3: ('💰', 'Hakuna malipo', 'Hakuna booking iliyolipwa leo.'),
    };
    final config = emptyConfig[_selectedTab]!;

    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(config.$1, style: const TextStyle(fontSize: 60))
              .animate(onPlay: (c) => c.repeat(reverse: true))
              .scaleXY(end: 1.1, duration: 2000.ms, curve: Curves.easeInOut),
          const SizedBox(height: 20),
          Text(
            config.$2,
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: AppTheme.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            config.$3,
            style: GoogleFonts.poppins(
              fontSize: 13,
              color: AppTheme.textSecondary,
            ),
          ),
        ],
      ),
    );
  }
}
