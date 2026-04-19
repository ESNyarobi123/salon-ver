import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../theme/app_theme.dart';
import '../utils/booking_time_format.dart';

class OrderCard extends StatelessWidget {
  final Order order;
  final VoidCallback onTap;
  final void Function(String) onUpdateStatus;
  final VoidCallback onDelete;

  const OrderCard({
    super.key,
    required this.order,
    required this.onTap,
    required this.onUpdateStatus,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final statusColor = AppTheme.getStatusColor(order.status);
    final statusIcon = AppTheme.getStatusIcon(order.status);
    final currency = NumberFormat('#,##0', 'en_US');
    final isTablet = MediaQuery.of(context).size.width > 600;
    final apptFmt = DateFormat('EEE, d MMM · HH:mm');

    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppTheme.border),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header row
            Container(
              padding: const EdgeInsets.fromLTRB(14, 12, 10, 10),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.05),
                borderRadius:
                    const BorderRadius.vertical(top: Radius.circular(15)),
                border: Border(
                    bottom: BorderSide(color: statusColor.withOpacity(0.15))),
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.12),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(statusIcon, color: statusColor, size: 16),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Text(
                              'Booking #${order.id}',
                              style: GoogleFonts.poppins(
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 7, vertical: 2),
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                order.status.toUpperCase(),
                                style: GoogleFonts.poppins(
                                  fontSize: 9,
                                  fontWeight: FontWeight.w700,
                                  color: statusColor,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ),
                          ],
                        ),
                        Row(
                          children: [
                            const Icon(Icons.chair_outlined,
                                size: 11, color: AppTheme.textMuted),
                            const SizedBox(width: 3),
                            Text(
                              '${SalonStrings.labelSeat} ${order.tableNumber}',
                              style: GoogleFonts.poppins(
                                fontSize: 11,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                            if (order.customerName != null &&
                                order.customerName!.isNotEmpty) ...[
                              const Text('  ·  ',
                                  style: TextStyle(color: AppTheme.textMuted)),
                              Flexible(
                                child: Text(
                                  order.customerName!,
                                  style: GoogleFonts.poppins(
                                    fontSize: 11,
                                    color: AppTheme.textSecondary,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Tooltip(
                        message:
                            'Booking: ${DateFormat('yyyy-MM-dd HH:mm').format(order.createdAt.toLocal())}',
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppTheme.surfaceVariant,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: AppTheme.border),
                          ),
                          child: Text(
                            formatBookingAgeShort(order.createdAt),
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textSecondary,
                              letterSpacing: 0.3,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 4),
                      PopupMenuButton<String>(
                        padding: EdgeInsets.zero,
                        color: AppTheme.surfaceVariant,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                        itemBuilder: (_) => [
                          if (order.status == 'pending')
                            PopupMenuItem(
                              value: 'preparing',
                              child: _menuItem(Icons.auto_fix_high_rounded,
                                  SalonStrings.statusStartService,
                                  AppTheme.statusPreparing),
                            ),
                          if (order.status == 'preparing')
                            PopupMenuItem(
                              value: 'served',
                              child: _menuItem(Icons.room_service_rounded,
                                  SalonStrings.statusCompleteService,
                                  AppTheme.statusServed),
                            ),
                          if (order.status == 'served')
                            PopupMenuItem(
                              value: 'paid',
                              child: _menuItem(Icons.payments_rounded,
                                  SalonStrings.statusConfirmPayment,
                                  AppTheme.statusPaid),
                            ),
                          const PopupMenuDivider(),
                          PopupMenuItem(
                            value: 'delete',
                            child: _menuItem(Icons.delete_outline_rounded,
                                SalonStrings.deleteBookingTooltip,
                                AppTheme.error),
                          ),
                        ],
                        onSelected: (v) {
                          HapticFeedback.selectionClick();
                          if (v == 'delete') {
                            onDelete();
                          } else {
                            onUpdateStatus(v);
                          }
                        },
                        child: const Icon(Icons.more_vert_rounded,
                            color: AppTheme.textMuted, size: 20),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            if (order.scheduledAt != null)
              Padding(
                padding: const EdgeInsets.fromLTRB(12, 0, 12, 8),
                child: Container(
                  width: double.infinity,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    gradient: LinearGradient(
                      colors: [
                        AppTheme.accent.withOpacity(0.18),
                        AppTheme.primary.withOpacity(0.12),
                      ],
                    ),
                    border: Border.all(
                        color: AppTheme.accent.withOpacity(0.35)),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.event_available_rounded,
                          color: AppTheme.accent.withOpacity(0.95), size: 20),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              SalonStrings.labelAppointment,
                              style: GoogleFonts.poppins(
                                fontSize: 9,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textMuted,
                                letterSpacing: 0.6,
                              ),
                            ),
                            Text(
                              apptFmt.format(order.scheduledAt!.toLocal()),
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                fontWeight: FontWeight.w600,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),

            // Items summary
            Padding(
              padding: const EdgeInsets.fromLTRB(14, 10, 14, 8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ...order.items.take(isTablet ? 2 : 3).map(
                        (item) => Padding(
                          padding: const EdgeInsets.only(bottom: 3),
                          child: Row(
                            children: [
                              Text('${item.quantity}×',
                                  style: GoogleFonts.poppins(
                                    fontSize: 12,
                                    color: AppTheme.primary,
                                    fontWeight: FontWeight.w600,
                                  )),
                              const SizedBox(width: 6),
                              Expanded(
                                child: Text(
                                  item.name,
                                  style: GoogleFonts.poppins(
                                    fontSize: 12,
                                    color: AppTheme.textSecondary,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                              Text(
                                'TZS ${NumberFormat('#,##0').format(item.total)}',
                                style: GoogleFonts.poppins(
                                  fontSize: 11,
                                  color: AppTheme.textMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                  if (order.items.length > (isTablet ? 2 : 3))
                    Text(
                      SalonStrings.moreItemsLine(
                          order.items.length - (isTablet ? 2 : 3)),
                      style: GoogleFonts.poppins(
                          fontSize: 11, color: AppTheme.textMuted),
                    ),
                ],
              ),
            ),

            // Footer
            Container(
              padding: const EdgeInsets.fromLTRB(14, 8, 14, 12),
              decoration: const BoxDecoration(
                border: Border(top: BorderSide(color: AppTheme.border)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.restaurant_menu_rounded,
                          size: 13, color: AppTheme.textMuted),
                      const SizedBox(width: 4),
                      Text(
                        SalonStrings.linesCount(order.items.length),
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: AppTheme.textMuted,
                        ),
                      ),
                    ],
                  ),
                  Text(
                    'TZS ${currency.format(order.totalAmount)}',
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _menuItem(IconData icon, String label, Color color) {
    return Row(
      children: [
        Icon(icon, color: color, size: 18),
        const SizedBox(width: 10),
        Text(label,
            style:
                GoogleFonts.poppins(fontSize: 13, color: AppTheme.textPrimary)),
      ],
    );
  }
}
