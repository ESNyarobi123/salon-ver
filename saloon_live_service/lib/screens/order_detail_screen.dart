import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'payment_screen.dart';

class OrderDetailScreen extends StatefulWidget {
  final Order order;
  final List<MenuItem> menuItems;
  final VoidCallback onUpdate;

  const OrderDetailScreen({
    super.key,
    required this.order,
    required this.menuItems,
    required this.onUpdate,
  });

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  final _api = ApiService();
  late Order _order;
  bool _isUpdating = false;

  @override
  void initState() {
    super.initState();
    _order = widget.order;
  }

  Future<void> _updateStatus(String newStatus) async {
    setState(() => _isUpdating = true);
    HapticFeedback.lightImpact();
    try {
      final updated = await _api.updateOrderStatus(_order.id, newStatus);
      setState(() {
        _order = updated;
        _isUpdating = false;
      });
      widget.onUpdate();
      _showSnack(
        SalonStrings.snackBookingStatusUpdated(_order.id, newStatus),
        AppTheme.success,
      );
    } catch (e) {
      setState(() => _isUpdating = false);
      _showSnack(e.toString(), AppTheme.error);
    }
  }

  Future<void> _delete() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        backgroundColor: AppTheme.surface,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(SalonStrings.deleteBookingTitle(_order.id),
            style: GoogleFonts.poppins(
                color: AppTheme.textPrimary, fontWeight: FontWeight.w600)),
        content: Text(SalonStrings.deleteBookingBody,
            style: GoogleFonts.poppins(color: AppTheme.textSecondary)),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text(SalonStrings.dialogNo)),
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
        await _api.deleteOrder(_order.id);
        widget.onUpdate();
        if (mounted) Navigator.pop(context);
      } catch (e) {
        _showSnack(e.toString(), AppTheme.error);
      }
    }
  }

  void _showSnack(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: color.withOpacity(0.9),
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }

  void _openPaymentScreen() {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => PaymentScreen(
          order: _order,
          onPaid: () {
            widget.onUpdate();
            Navigator.of(context).popUntil(
                (route) => route.isFirst || route.settings.name == '/orders');
          },
        ),
      ),
    );
  }

  List<String> get _nextStatuses {
    switch (_order.status) {
      case 'pending':
        return ['preparing'];
      case 'preparing':
        return ['served'];
      case 'served':
        return ['paid'];
      default:
        return [];
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isTablet = size.width > 600;
    final currency = NumberFormat('#,##0', 'en_US');
    final dateFormat = DateFormat('dd MMM yyyy, HH:mm');
    final statusColor = AppTheme.getStatusColor(_order.status);
    final statusIcon = AppTheme.getStatusIcon(_order.status);

    return Scaffold(
      backgroundColor: AppTheme.bg,
      body: CustomScrollView(
        slivers: [
          // Hero AppBar
          SliverAppBar(
            expandedHeight: 200,
            pinned: true,
            backgroundColor: AppTheme.surface,
            leading: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded,
                  color: AppTheme.textPrimary),
              onPressed: () => Navigator.pop(context),
            ),
            actions: [
              IconButton(
                onPressed: _delete,
                icon: const Icon(Icons.delete_outline_rounded,
                    color: AppTheme.error),
                tooltip: SalonStrings.deleteBookingTooltip,
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      statusColor.withOpacity(0.2),
                      AppTheme.surface,
                    ],
                  ),
                ),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 56, 20, 16),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // Status icon
                        AnimatedContainer(
                          duration: const Duration(milliseconds: 300),
                          width: isTablet ? 80 : 64,
                          height: isTablet ? 80 : 64,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: statusColor.withOpacity(0.15),
                            border: Border.all(color: statusColor, width: 2),
                          ),
                          child: Icon(statusIcon,
                              color: statusColor, size: isTablet ? 38 : 30),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(
                                SalonStrings.bookingHeaderLine(_order.id),
                                style: GoogleFonts.poppins(
                                  fontSize: isTablet ? 28 : 22,
                                  fontWeight: FontWeight.w800,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 3),
                                decoration: BoxDecoration(
                                  color: statusColor.withOpacity(0.15),
                                  borderRadius: BorderRadius.circular(20),
                                  border: Border.all(
                                      color: statusColor.withOpacity(0.4)),
                                ),
                                child: Text(
                                  _order.status.toUpperCase(),
                                  style: GoogleFonts.poppins(
                                    fontSize: 11,
                                    fontWeight: FontWeight.w700,
                                    color: statusColor,
                                    letterSpacing: 1,
                                  ),
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                dateFormat.format(_order.createdAt.toLocal()),
                                style: GoogleFonts.poppins(
                                  fontSize: 12,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),

          SliverPadding(
            padding: EdgeInsets.all(isTablet ? 24 : 16),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                // Info cards
                _buildInfoRow(isTablet),
                const SizedBox(height: 16),

                // Items
                _buildItemsList(currency).animate().fadeIn(delay: 200.ms),
                const SizedBox(height: 16),

                // Total
                _buildTotalCard(currency).animate().fadeIn(delay: 300.ms),
                const SizedBox(height: 16),

                // Status actions
                if (_isUpdating)
                  const Center(
                      child: CircularProgressIndicator(color: AppTheme.primary))
                else ...[
                  if (_nextStatuses.isNotEmpty)
                    _buildStatusActions().animate().fadeIn(delay: 400.ms),
                  const SizedBox(height: 12),
                  if (_order.status == 'served')
                    _buildPaymentButton().animate().fadeIn(delay: 450.ms),
                ],

                const SizedBox(height: 80),
              ]),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(bool isTablet) {
    final infos = [
      (Icons.chair_outlined, SalonStrings.labelSeat, _order.tableNumber),
      if (_order.customerName != null && _order.customerName!.isNotEmpty)
        (Icons.person_outline_rounded, SalonStrings.labelClient, _order.customerName!),
      if (_order.customerPhone != null && _order.customerPhone!.isNotEmpty)
        (Icons.phone_outlined, SalonStrings.labelPhone, _order.customerPhone!),
    ];

    return Wrap(
      spacing: 10,
      runSpacing: 10,
      children: infos
          .map((info) => Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                decoration: BoxDecoration(
                  color: AppTheme.surface,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(info.$1, color: AppTheme.primary, size: 16),
                    const SizedBox(width: 8),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(info.$2,
                            style: GoogleFonts.poppins(
                                fontSize: 10, color: AppTheme.textMuted)),
                        Text(info.$3,
                            style: GoogleFonts.poppins(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppTheme.textPrimary)),
                      ],
                    ),
                  ],
                ),
              ))
          .toList(),
    ).animate().fadeIn(delay: 100.ms).slideY(begin: 0.1);
  }

  Widget _buildItemsList(NumberFormat currency) {
    return Container(
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Text(
              '${SalonStrings.sectionServicesProducts} (${_order.items.length})',
              style: GoogleFonts.poppins(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: AppTheme.textPrimary,
              ),
            ),
          ),
          const Divider(height: 1, color: AppTheme.border),
          ...List.generate(_order.items.length, (i) {
            final item = _order.items[i];
            return Column(
              children: [
                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  child: Row(
                    children: [
                      Container(
                        width: 36,
                        height: 36,
                        decoration: BoxDecoration(
                          color: AppTheme.primary.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Center(
                          child: Text(
                            '${item.quantity}',
                            style: GoogleFonts.poppins(
                              fontWeight: FontWeight.w700,
                              color: AppTheme.primary,
                              fontSize: 14,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(item.name,
                                style: GoogleFonts.poppins(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.textPrimary,
                                )),
                            Text(
                              'TZS ${currency.format(item.price)} × ${item.quantity}',
                              style: GoogleFonts.poppins(
                                fontSize: 11,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Text(
                        'TZS ${currency.format(item.total)}',
                        style: GoogleFonts.poppins(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                    ],
                  ),
                ),
                if (i < _order.items.length - 1)
                  const Divider(
                      height: 1,
                      color: AppTheme.border,
                      indent: 16,
                      endIndent: 16),
              ],
            );
          }),
        ],
      ),
    );
  }

  Widget _buildTotalCard(NumberFormat currency) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppTheme.success.withOpacity(0.1),
            AppTheme.success.withOpacity(0.05),
          ],
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.success.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              const Icon(Icons.receipt_long_rounded,
                  color: AppTheme.success, size: 22),
              const SizedBox(width: 10),
              Text(SalonStrings.totalBill,
                  style: GoogleFonts.poppins(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.textPrimary,
                  )),
            ],
          ),
          Text(
            'TZS ${currency.format(_order.totalAmount)}',
            style: GoogleFonts.poppins(
              fontSize: 20,
              fontWeight: FontWeight.w800,
              color: AppTheme.success,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusActions() {
    final statusLabels = {
      'preparing': (
        SalonStrings.statusStartService,
        AppTheme.statusPreparing,
        Icons.auto_fix_high_rounded
      ),
      'served': (
        SalonStrings.statusCompleteService,
        AppTheme.statusServed,
        Icons.check_circle_outline_rounded
      ),
      'paid': (
        SalonStrings.statusConfirmPayment,
        AppTheme.statusPaid,
        Icons.payments_rounded
      ),
    };

    return Column(
      children: _nextStatuses.map((status) {
        final config = statusLabels[status]!;
        return Padding(
          padding: const EdgeInsets.only(bottom: 8),
          child: SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: config.$2.withOpacity(0.15),
                foregroundColor: config.$2,
                side: BorderSide(color: config.$2.withOpacity(0.4)),
                elevation: 0,
              ),
              onPressed: () => _updateStatus(status),
              icon: Icon(config.$3, size: 18),
              label: Text(config.$1,
                  style: GoogleFonts.poppins(fontWeight: FontWeight.w600)),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildPaymentButton() {
    return SizedBox(
      width: double.infinity,
      height: 52,
      child: ElevatedButton.icon(
        onPressed: _openPaymentScreen,
        style: ElevatedButton.styleFrom(
          backgroundColor: AppTheme.primary,
          foregroundColor: Colors.white,
        ),
        icon: const Icon(Icons.phone_in_talk_rounded, size: 20),
        label: Text(
          SalonStrings.paySelcomUssd,
          style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w700),
        ),
      ),
    );
  }
}
