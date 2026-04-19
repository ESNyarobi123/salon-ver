import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';

class PaymentScreen extends StatefulWidget {
  final Order order;
  final VoidCallback onPaid;

  /// Retail product sale (uses `/product-sales/.../payments/selcom/*`).
  final bool isProductSale;

  const PaymentScreen({
    super.key,
    required this.order,
    required this.onPaid,
    this.isProductSale = false,
  });

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen>
    with TickerProviderStateMixin {
  final _api = ApiService();
  final _phoneController = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  bool _isSending = false;
  bool _isPolling = false;
  bool _paymentSent = false;
  String _paymentStatus = '';
  String _statusMessage = '';
  String? _transactionId;
  Timer? _pollTimer;
  int _pollCount = 0;
  static const int _maxPolls = 12; // 60 seconds

  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    // Pre-fill phone from order
    if (widget.order.customerPhone != null &&
        widget.order.customerPhone!.isNotEmpty) {
      _phoneController.text = widget.order.customerPhone!;
    }

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _phoneController.dispose();
    _pollTimer?.cancel();
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _initiatePayment() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSending = true);
    HapticFeedback.lightImpact();

    try {
      final result = widget.isProductSale
          ? await _api.initiateProductPayment(
              orderId: widget.order.id,
              phone: _phoneController.text.trim(),
              name: widget.order.customerName,
            )
          : await _api.initiatePayment(
              orderId: widget.order.id,
              phone: _phoneController.text.trim(),
              name: widget.order.customerName,
            );

      if (mounted) {
        if (result['status'] == 'success') {
          setState(() {
            _paymentSent = true;
            _transactionId = result['transaction_id'];
            _isSending = false;
            _paymentStatus = 'pending';
            _statusMessage = SalonStrings.waitClientUssd;
          });
          _startPolling();
        } else {
          setState(() {
            _isSending = false;
            _paymentStatus = 'error';
            _statusMessage =
                result['message'] ?? SalonStrings.ussdFailedGeneric;
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isSending = false;
          _paymentStatus = 'error';
          _statusMessage = e.toString();
        });
      }
    }
  }

  void _startPolling() {
    setState(() {
      _isPolling = true;
      _pollCount = 0;
    });

    _pollTimer = Timer.periodic(const Duration(seconds: 5), (_) async {
      if (_pollCount >= _maxPolls) {
        _pollTimer?.cancel();
        if (mounted) {
          setState(() {
            _isPolling = false;
            _paymentStatus = 'timeout';
            _statusMessage = SalonStrings.paymentTimeoutMessage;
          });
        }
        return;
      }

      _pollCount++;

      try {
        final result = widget.isProductSale
            ? await _api.getProductPaymentStatus(widget.order.id)
            : await _api.getPaymentStatus(widget.order.id);
        final status = result['status'] as String? ?? 'pending';

        if (mounted) {
          setState(() {
            _paymentStatus = status;
            _statusMessage = result['message'] ?? '';
          });

          if (status == 'paid') {
            _pollTimer?.cancel();
            HapticFeedback.heavyImpact();
            setState(() => _isPolling = false);
          } else if (status == 'failed') {
            _pollTimer?.cancel();
            setState(() => _isPolling = false);
          }
        }
      } catch (_) {}
    });
  }

  void _confirmCash() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        backgroundColor: AppTheme.surface,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(SalonStrings.confirmCashPaymentTitle,
            style: GoogleFonts.poppins(
                color: AppTheme.textPrimary, fontWeight: FontWeight.w700)),
        content: Text(
          SalonStrings.confirmCashPaymentBody,
          style: GoogleFonts.poppins(color: AppTheme.textSecondary),
        ),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text(SalonStrings.dialogNo)),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text(SalonStrings.confirmYesPaid),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        await _api.updateOrderStatus(widget.order.id, 'paid');
        widget.onPaid();
        HapticFeedback.heavyImpact();
        if (mounted) Navigator.pop(context);
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(e.toString()),
              backgroundColor: AppTheme.error.withOpacity(0.9),
            ),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isTablet = size.width > 600;
    final currency = NumberFormat('#,##0', 'en_US');

    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        backgroundColor: AppTheme.surface,
        title: Text(
            '${widget.isProductSale ? SalonStrings.paymentAppBarProduct : SalonStrings.paymentAppBar} #${widget.order.id}',
            style: GoogleFonts.poppins(
                fontSize: 17,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary)),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textPrimary),
          onPressed: () {
            _pollTimer?.cancel();
            Navigator.pop(context);
          },
        ),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppTheme.border),
        ),
      ),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(isTablet ? 32 : 20),
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 500),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Order summary
                _buildOrderSummary(currency).animate().fadeIn(delay: 100.ms),
                const SizedBox(height: 24),

                if (!_paymentSent) ...[
                  _buildInitiateForm().animate().fadeIn(delay: 200.ms),
                ] else ...[
                  _buildPaymentStatus().animate().fadeIn(duration: 400.ms),
                  const SizedBox(height: 16),
                  if (_paymentStatus == 'paid') _buildSuccessCard(),
                ],

                const SizedBox(height: 24),

                // Cash confirm
                if (!widget.isProductSale && widget.order.status == 'served')
                  _buildCashOption().animate().fadeIn(delay: 300.ms),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildOrderSummary(NumberFormat currency) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
      ),
      child: Row(
        children: [
          const Icon(Icons.receipt_rounded, color: AppTheme.primary, size: 36),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                    widget.isProductSale
                        ? SalonStrings.productSaleLine(
                            widget.order.id, widget.order.tableNumber)
                        : SalonStrings.bookingSeatLine(
                            widget.order.id, widget.order.tableNumber),
                    style: GoogleFonts.poppins(
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        fontSize: 14)),
                Text(
                  '${widget.order.items.length} ${SalonStrings.orderSummaryLines} · ${widget.order.customerName ?? SalonStrings.labelClient}',
                  style: GoogleFonts.poppins(
                      fontSize: 12, color: AppTheme.textSecondary),
                ),
              ],
            ),
          ),
          Text(
            'TZS\n${currency.format(widget.order.totalAmount)}',
            style: GoogleFonts.poppins(
              fontSize: 15,
              fontWeight: FontWeight.w800,
              color: AppTheme.success,
            ),
            textAlign: TextAlign.right,
          ),
        ],
      ),
    );
  }

  Widget _buildInitiateForm() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
      ),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppTheme.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.phone_in_talk_rounded,
                      color: AppTheme.primary, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(SalonStrings.ussdPushTitle,
                          style: GoogleFonts.poppins(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary)),
                      Text(SalonStrings.ussdPushSubtitle,
                          style: GoogleFonts.poppins(
                              fontSize: 12, color: AppTheme.textSecondary)),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 20),
            TextFormField(
              controller: _phoneController,
              keyboardType: TextInputType.phone,
              style: const TextStyle(color: AppTheme.textPrimary),
              decoration: const InputDecoration(
                labelText: SalonStrings.phoneNumberLabel,
                hintText: SalonStrings.hintPhoneLong,
                prefixIcon:
                    Icon(Icons.phone_outlined, color: AppTheme.textSecondary),
                helperText: SalonStrings.phoneNumberHelper,
              ),
              validator: (v) {
                if (v == null || v.trim().isEmpty) {
                  return SalonStrings.phoneRequiredValidation;
                }
                return null;
              },
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton.icon(
                onPressed: _isSending ? null : _initiatePayment,
                icon: _isSending
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                            color: Colors.white, strokeWidth: 2))
                    : const Icon(Icons.send_rounded),
                label: Text(
                  _isSending
                      ? SalonStrings.sendingUssd
                      : SalonStrings.sendUssdPush,
                  style: GoogleFonts.poppins(
                      fontWeight: FontWeight.w700, fontSize: 15),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPaymentStatus() {
    final isPaid = _paymentStatus == 'paid';
    final isFailed = _paymentStatus == 'failed' || _paymentStatus == 'timeout';
    final color = isPaid
        ? AppTheme.success
        : isFailed
            ? AppTheme.error
            : AppTheme.info;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          if (_isPolling)
            AnimatedBuilder(
              animation: _pulseController,
              builder: (_, child) => Transform.scale(
                scale: 1.0 + (_pulseController.value * 0.15),
                child: child,
              ),
              child: Container(
                width: 60,
                height: 60,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppTheme.info.withOpacity(0.2),
                  border: Border.all(color: AppTheme.info, width: 2),
                ),
                child: const Icon(Icons.hourglass_top_rounded,
                    color: AppTheme.info, size: 28),
              ),
            )
          else
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: color.withOpacity(0.15),
              ),
              child: Icon(
                isPaid ? Icons.check_circle_rounded : Icons.cancel_rounded,
                color: color,
                size: 32,
              ),
            ),
          const SizedBox(height: 12),
          Text(
            isPaid
                ? SalonStrings.paymentSuccessTitle
                : isFailed
                    ? SalonStrings.paymentFailedTitle
                    : SalonStrings.paymentWaitingTitle,
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: color,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            _statusMessage,
            style: GoogleFonts.poppins(
                fontSize: 13, color: AppTheme.textSecondary),
            textAlign: TextAlign.center,
          ),
          if (_transactionId != null) ...[
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: AppTheme.surfaceVariant,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                SalonStrings.paymentTxLine(_transactionId!),
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: AppTheme.textSecondary,
                ),
              ),
            ),
          ],
          if (_isPolling) ...[
            const SizedBox(height: 12),
            const LinearProgressIndicator(
              backgroundColor: AppTheme.border,
              valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primary),
            ),
            const SizedBox(height: 6),
            Text(
              SalonStrings.paymentPollLine(_maxPolls - _pollCount),
              style:
                  GoogleFonts.poppins(fontSize: 11, color: AppTheme.textMuted),
            ),
          ],
          if (isFailed) ...[
            const SizedBox(height: 16),
            OutlinedButton.icon(
              onPressed: () => setState(() {
                _paymentSent = false;
                _paymentStatus = '';
                _statusMessage = '';
                _transactionId = null;
              }),
              icon: const Icon(Icons.refresh_rounded),
              label: const Text(SalonStrings.tryAgain),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildSuccessCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppTheme.success.withOpacity(0.15),
            AppTheme.success.withOpacity(0.05),
          ],
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.success.withOpacity(0.4)),
      ),
      child: ElevatedButton.icon(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppTheme.success,
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(vertical: 14),
        ),
        onPressed: () {
          widget.onPaid();
          Navigator.pop(context);
        },
        icon: const Icon(Icons.check_circle_rounded),
        label: Text(SalonStrings.closeAndReturn,
            style:
                GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w700)),
      ),
    );
  }

  Widget _buildCashOption() {
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
          Row(
            children: [
              const Icon(Icons.payments_outlined,
                  color: AppTheme.warning, size: 20),
              const SizedBox(width: 8),
              Text(SalonStrings.externalPaymentTitle,
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.textPrimary,
                  )),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            SalonStrings.externalPaymentHint,
            style: GoogleFonts.poppins(
                fontSize: 12, color: AppTheme.textSecondary),
          ),
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            height: 44,
            child: OutlinedButton.icon(
              style: OutlinedButton.styleFrom(
                foregroundColor: AppTheme.warning,
                side: BorderSide(color: AppTheme.warning.withOpacity(0.5)),
              ),
              onPressed: _confirmCash,
              icon: const Icon(Icons.check_rounded, size: 18),
              label: Text(SalonStrings.confirmPaidCash,
                  style: GoogleFonts.poppins(fontWeight: FontWeight.w600)),
            ),
          ),
        ],
      ),
    );
  }
}
