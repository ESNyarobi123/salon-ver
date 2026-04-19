import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';

class ProductSalesScreen extends StatefulWidget {
  const ProductSalesScreen({super.key});

  @override
  State<ProductSalesScreen> createState() => _ProductSalesScreenState();
}

class _ProductSalesScreenState extends State<ProductSalesScreen> {
  final _api = ApiService();
  ProductSalesData? _data;
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _confirmCancelPending(int orderId) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppTheme.surface,
        title: Text(SalonStrings.cancelPendingProductTitle,
            style: GoogleFonts.poppins(
                fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
        content: Text(SalonStrings.cancelPendingProductBody,
            style: GoogleFonts.poppins(color: AppTheme.textSecondary)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text(SalonStrings.dialogNo),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text(SalonStrings.dialogCancelSale),
          ),
        ],
      ),
    );
    if (ok != true || !mounted) return;
    try {
      await _api.cancelProductSale(orderId);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Uzoaji umeghairiwa.'),
            backgroundColor: AppTheme.success.withValues(alpha: 0.9),
          ),
        );
        _load();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString()),
            backgroundColor: AppTheme.error.withValues(alpha: 0.9),
          ),
        );
      }
    }
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final d = await _api.getProductSales();
      if (mounted) {
        setState(() {
          _data = d;
          _loading = false;
        });
      }
    } on UnauthorizedException {
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e.toString();
          _loading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat('#,##0', 'en_US');

    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        backgroundColor: AppTheme.surface,
        title: Text(
          SalonStrings.productSalesTitle,
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.w700,
            color: AppTheme.textPrimary,
          ),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textPrimary),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded,
                color: AppTheme.textSecondary),
            onPressed: _load,
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppTheme.border),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : _error != null
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(_error!,
                            textAlign: TextAlign.center,
                            style: GoogleFonts.poppins(
                                color: AppTheme.textSecondary)),
                        const SizedBox(height: 16),
                        ElevatedButton(
                            onPressed: _load, child: const Text(SalonStrings.tryAgain)),
                      ],
                    ),
                  ),
                )
              : RefreshIndicator(
                  color: AppTheme.primary,
                  onRefresh: _load,
                  child: CustomScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    slivers: [
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                          child: _summaryCard(currency),
                        ),
                      ),
                      if (_data!.sales.isEmpty)
                        SliverFillRemaining(
                          hasScrollBody: false,
                          child: Center(
                            child: Text(
                              'Bado hakuna mauzo kwenye kipindi hiki.',
                              style: GoogleFonts.poppins(
                                  color: AppTheme.textSecondary),
                            ),
                          ),
                        )
                      else
                        SliverPadding(
                          padding: const EdgeInsets.fromLTRB(16, 0, 16, 32),
                          sliver: SliverList(
                            delegate: SliverChildBuilderDelegate(
                              (context, i) {
                                final s = _data!.sales[i];
                                return _saleTile(s, currency)
                                    .animate()
                                    .fadeIn(delay: (40 * i).ms);
                              },
                              childCount: _data!.sales.length,
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
    );
  }

  Widget _summaryCard(NumberFormat currency) {
    final sum = _data!.summary;
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            SalonStrings.productSalesSubtitle,
            style: GoogleFonts.poppins(
              fontSize: 12,
              color: AppTheme.textSecondary,
            ),
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: _sumBlock(
                  SalonStrings.revenueTodayLabel,
                  'TZS ${currency.format(sum.totalAmountToday)}',
                  AppTheme.success,
                ),
              ),
              Expanded(
                child: _sumBlock(
                  SalonStrings.salesCountToday,
                  '${sum.countToday}',
                  AppTheme.primary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _sumBlock(
            SalonStrings.revenue30dLabel,
            'TZS ${currency.format(sum.totalAmountLast30Days)} · ${sum.countLast30Days} mauzo',
            AppTheme.textPrimary,
            wide: true,
          ),
        ],
      ),
    );
  }

  Widget _sumBlock(String label, String value, Color accent,
      {bool wide = false}) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: Column(
        crossAxisAlignment:
            wide ? CrossAxisAlignment.start : CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: GoogleFonts.poppins(
              fontSize: 11,
              color: AppTheme.textMuted,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: GoogleFonts.poppins(
              fontSize: wide ? 14 : 15,
              fontWeight: FontWeight.w700,
              color: accent,
            ),
          ),
        ],
      ),
    );
  }

  Widget _saleTile(ProductSaleRecord s, NumberFormat currency) {
    final statusColor = s.status == 'paid'
        ? AppTheme.success
        : s.status == 'payment_pending'
            ? AppTheme.warning
            : AppTheme.textSecondary;
    final statusLabel = s.status == 'payment_pending'
        ? SalonStrings.statusAwaitingPush
        : s.status;

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      color: AppTheme.surface,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(14),
        side: const BorderSide(color: AppTheme.border),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: () {
          HapticFeedback.selectionClick();
          _showDetail(s, currency);
        },
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      SalonStrings.productSaleLine(s.id, s.tableNumber),
                      style: GoogleFonts.poppins(
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        fontSize: 14,
                      ),
                    ),
                  ),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      statusLabel,
                      style: GoogleFonts.poppins(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: statusColor,
                      ),
                    ),
                  ),
                  if (s.status == 'payment_pending') ...[
                    const SizedBox(width: 4),
                    IconButton(
                      tooltip: SalonStrings.dialogCancelSale,
                      visualDensity: VisualDensity.compact,
                      onPressed: () => _confirmCancelPending(s.id),
                      icon: const Icon(Icons.close_rounded,
                          color: AppTheme.error, size: 22),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 6),
              Text(
                DateFormat('d MMM yyyy, HH:mm').format(s.createdAt.toLocal()),
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: AppTheme.textMuted,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                s.items.map((e) => '${e.quantity}× ${e.name}').join(' · '),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: AppTheme.textSecondary,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'TZS ${currency.format(s.totalAmount)}',
                style: GoogleFonts.poppins(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: AppTheme.success,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showDetail(ProductSaleRecord s, NumberFormat currency) {
    showModalBottomSheet<void>(
      context: context,
      backgroundColor: AppTheme.surface,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              SalonStrings.productSaleLine(s.id, s.tableNumber),
              style: GoogleFonts.poppins(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
              ),
            ),
            const SizedBox(height: 12),
            ...s.items.map(
              (e) => Padding(
                padding: const EdgeInsets.only(bottom: 6),
                child: Row(
                  children: [
                    Text('${e.quantity}×',
                        style: GoogleFonts.poppins(
                            color: AppTheme.textSecondary, fontSize: 13)),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        e.name,
                        style: GoogleFonts.poppins(
                            color: AppTheme.textPrimary, fontSize: 13),
                      ),
                    ),
                    Text(
                      'TZS ${currency.format(e.total)}',
                      style: GoogleFonts.poppins(
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const Divider(height: 24),
            Text(
              'TZS ${currency.format(s.totalAmount)}',
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: AppTheme.success,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
