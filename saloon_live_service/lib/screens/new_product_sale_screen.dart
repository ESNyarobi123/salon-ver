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

class NewProductSaleScreen extends StatefulWidget {
  final List<TableInfo> tables;
  final List<BookingCategory> productCategories;
  final VoidCallback onDone;

  const NewProductSaleScreen({
    super.key,
    required this.tables,
    required this.productCategories,
    required this.onDone,
  });

  @override
  State<NewProductSaleScreen> createState() => _NewProductSaleScreenState();
}

class _NewProductSaleScreenState extends State<NewProductSaleScreen> {
  final _api = ApiService();
  final _tableController = TextEditingController();
  final _phoneController = TextEditingController();
  final _nameController = TextEditingController();
  final _pushPhoneController = TextEditingController();
  final _searchController = TextEditingController();

  int _step = 0;
  String? _selectedSeat;
  final Map<int, int> _cart = {};
  bool _loading = false;
  String _search = '';
  bool _payCash = true;

  Iterable<MenuItem> get _allItems sync* {
    for (final c in widget.productCategories) {
      yield* c.items;
    }
  }

  double get _total => _cart.entries.fold(0.0, (sum, e) {
        MenuItem? m;
        for (final x in _allItems) {
          if (x.id == e.key) {
            m = x;
            break;
          }
        }
        if (m == null) return sum;
        return sum + m.price * e.value;
      });

  @override
  void initState() {
    super.initState();
    if (widget.tables.isNotEmpty) {
      _selectedSeat = widget.tables.first.name;
      _tableController.text = _selectedSeat!;
    } else {
      _tableController.text = 'Retail';
    }
  }

  @override
  void dispose() {
    _tableController.dispose();
    _phoneController.dispose();
    _nameController.dispose();
    _pushPhoneController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _add(MenuItem item) {
    HapticFeedback.selectionClick();
    setState(() => _cart[item.id] = (_cart[item.id] ?? 0) + 1);
  }

  void _remove(MenuItem item) {
    HapticFeedback.selectionClick();
    setState(() {
      if ((_cart[item.id] ?? 0) <= 1) {
        _cart.remove(item.id);
      } else {
        _cart[item.id] = _cart[item.id]! - 1;
      }
    });
  }

  List<Map<String, int>> get _itemsPayload =>
      _cart.entries.map((e) => {'id': e.key, 'quantity': e.value}).toList();

  Future<void> _submit() async {
    if (_cart.isEmpty) return;
    if (!_payCash) {
      if (_pushPhoneController.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text(SalonStrings.pushPhoneLabel),
            backgroundColor: AppTheme.error.withValues(alpha: 0.9),
          ),
        );
        return;
      }
    }

    setState(() => _loading = true);
    try {
      final record = await _api.createProductSale(
        tableNumber: _tableController.text.trim().isEmpty
            ? null
            : _tableController.text.trim(),
        customerPhone: _phoneController.text.trim().isEmpty
            ? null
            : _phoneController.text.trim(),
        customerName: _nameController.text.trim().isEmpty
            ? null
            : _nameController.text.trim(),
        items: _itemsPayload,
        payment: _payCash ? 'cash' : 'push',
        pushPhone:
            _payCash ? null : _pushPhoneController.text.trim(),
      );

      if (!mounted) return;

      if (_payCash) {
        widget.onDone();
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text(SalonStrings.snackProductSaleRecorded),
            backgroundColor: AppTheme.success.withValues(alpha: 0.9),
          ),
        );
      } else {
        await Navigator.push<void>(
          context,
          MaterialPageRoute(
            builder: (_) => PaymentScreen(
              order: record.toOrderForPaymentUi(),
              isProductSale: true,
              onPaid: () {
                widget.onDone();
              },
            ),
          ),
        );
        if (mounted) Navigator.pop(context);
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
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isTablet = size.width > 600;
    final currency = NumberFormat('#,##0', 'en_US');

    if (widget.productCategories.isEmpty) {
      return Scaffold(
        backgroundColor: AppTheme.bg,
        appBar: AppBar(
          backgroundColor: AppTheme.surface,
          title: Text(SalonStrings.newProductSaleTitle,
              style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
          leading: IconButton(
            icon: const Icon(Icons.close_rounded, color: AppTheme.textSecondary),
            onPressed: () => Navigator.pop(context),
          ),
        ),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Text(
              SalonStrings.noRetailProducts,
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(color: AppTheme.textSecondary),
            ),
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        backgroundColor: AppTheme.surface,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              SalonStrings.newProductSaleTitle,
              style: GoogleFonts.poppins(
                fontWeight: FontWeight.w700,
                fontSize: 17,
                color: AppTheme.textPrimary,
              ),
            ),
            Text(
              SalonStrings.newProductSaleSubtitle,
              style: GoogleFonts.poppins(
                fontSize: 11,
                color: AppTheme.textSecondary,
              ),
            ),
          ],
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textPrimary),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: isTablet
          ? (_step == 0
              ? Row(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Expanded(flex: 3, child: _buildStep0(currency)),
                    Container(width: 1, color: AppTheme.border),
                    Expanded(flex: 2, child: _cartSidebar(currency)),
                  ],
                )
              : Column(
                  children: [
                    _stepIndicator(),
                    Expanded(child: _buildStepPay(currency)),
                    _footer(currency, isTablet),
                  ],
                ))
          : Column(
              children: [
                _stepIndicator(),
                Expanded(
                  child: _step == 0
                      ? _buildStep0(currency)
                      : _buildStepPay(currency),
                ),
                _footer(currency, isTablet),
              ],
            ),
    );
  }

  Widget _stepIndicator() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
      child: Row(
        children: [
          _stepChip(0, SalonStrings.stepProductsRetail),
          const SizedBox(width: 8),
          _stepChip(1, SalonStrings.stepPayRetail),
        ],
      ),
    );
  }

  Widget _stepChip(int idx, String label) {
    final on = _step == idx;
    return Expanded(
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: on ? AppTheme.primary.withValues(alpha: 0.2) : AppTheme.surface,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: on ? AppTheme.primary : AppTheme.border,
          ),
        ),
        child: Text(
          label,
          textAlign: TextAlign.center,
          style: GoogleFonts.poppins(
            fontSize: 12,
            fontWeight: on ? FontWeight.w700 : FontWeight.w500,
            color: on ? AppTheme.primary : AppTheme.textSecondary,
          ),
        ),
      ),
    );
  }

  Widget _buildStep0(NumberFormat currency) {
    final q = _search.trim().toLowerCase();
    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
      children: [
        TextField(
          controller: _searchController,
          onChanged: (v) => setState(() => _search = v),
          style: const TextStyle(color: AppTheme.textPrimary),
          decoration: InputDecoration(
            hintText: SalonStrings.menuSearchProductsHint,
            prefixIcon:
                const Icon(Icons.search_rounded, color: AppTheme.textSecondary),
            filled: true,
            fillColor: AppTheme.surface,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: AppTheme.border),
            ),
          ),
        ),
        const SizedBox(height: 16),
        for (final cat in widget.productCategories) ...[
          if (cat.items.any(
              (m) => q.isEmpty || m.name.toLowerCase().contains(q))) ...[
            Text(
              cat.name,
              style: GoogleFonts.poppins(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
              ),
            ),
            const SizedBox(height: 8),
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                mainAxisExtent: 88,
                crossAxisSpacing: 10,
                mainAxisSpacing: 10,
              ),
              itemCount: cat.items
                  .where((m) =>
                      q.isEmpty || m.name.toLowerCase().contains(q))
                  .length,
              itemBuilder: (context, i) {
                final item = cat.items
                    .where((m) =>
                        q.isEmpty || m.name.toLowerCase().contains(q))
                    .elementAt(i);
                final n = _cart[item.id] ?? 0;
                return Material(
                  color: AppTheme.surface,
                  borderRadius: BorderRadius.circular(14),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(14),
                    onTap: () => _add(item),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 8),
                      child: Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  item.name,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: GoogleFonts.poppins(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: AppTheme.textPrimary,
                                  ),
                                ),
                                Text(
                                  'TZS ${currency.format(item.price)}',
                                  style: GoogleFonts.poppins(
                                    fontSize: 11,
                                    color: AppTheme.success,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          if (n > 0)
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                IconButton(
                                  visualDensity: VisualDensity.compact,
                                  onPressed: () => _remove(item),
                                  icon: const Icon(Icons.remove_circle_outline,
                                      color: AppTheme.error, size: 22),
                                ),
                                Text('$n',
                                    style: GoogleFonts.poppins(
                                        fontWeight: FontWeight.w800,
                                        fontSize: 14,
                                        color: AppTheme.primary)),
                                IconButton(
                                  visualDensity: VisualDensity.compact,
                                  onPressed: () => _add(item),
                                  icon: const Icon(Icons.add_circle_outline,
                                      color: AppTheme.primary, size: 22),
                                ),
                              ],
                            )
                          else
                            const Icon(Icons.add_rounded,
                                color: AppTheme.primary, size: 22),
                        ],
                      ),
                    ),
                  ),
                ).animate().fadeIn(duration: 200.ms);
              },
            ),
            const SizedBox(height: 16),
          ],
        ],
      ],
    );
  }

  Widget _cartSidebar(NumberFormat currency) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Padding(
          padding: const EdgeInsets.all(16),
          child: Text(
            SalonStrings.cart,
            style: GoogleFonts.poppins(
              fontWeight: FontWeight.w700,
              color: AppTheme.textPrimary,
            ),
          ),
        ),
        Expanded(
          child: ListView(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            children: [
              if (_cart.isEmpty)
                Text(
                  'Chagua bidhaa kushoto.',
                  style: GoogleFonts.poppins(color: AppTheme.textSecondary),
                )
              else
                ..._cart.entries.map((e) {
                  MenuItem? m;
                  for (final x in _allItems) {
                    if (x.id == e.key) m = x;
                  }
                  if (m == null) return const SizedBox.shrink();
                  return ListTile(
                    dense: true,
                    contentPadding: EdgeInsets.zero,
                    title: Text(m.name,
                        style: GoogleFonts.poppins(
                            color: AppTheme.textPrimary, fontSize: 13)),
                    subtitle: Text(
                      '${e.value}× TZS ${currency.format(m.price)}',
                      style: GoogleFonts.poppins(
                          fontSize: 11, color: AppTheme.textSecondary),
                    ),
                    trailing: Text(
                      'TZS ${currency.format(m.price * e.value)}',
                      style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w700,
                          fontSize: 12,
                          color: AppTheme.success),
                    ),
                  );
                }),
            ],
          ),
        ),
        Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(SalonStrings.total,
                  style: GoogleFonts.poppins(
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textSecondary)),
              Text(
                'TZS ${currency.format(_total)}',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w800,
                  fontSize: 16,
                  color: AppTheme.success,
                ),
              ),
            ],
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
          child: ElevatedButton(
            onPressed: _cart.isEmpty
                ? null
                : () => setState(() => _step = 1),
            child: const Text(SalonStrings.stepContinue),
          ),
        ),
      ],
    );
  }

  Widget _buildStepPay(NumberFormat currency) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        if (widget.tables.isNotEmpty)
          DropdownButtonFormField<String>(
            initialValue: _selectedSeat,
            decoration: const InputDecoration(labelText: SalonStrings.labelSeatStar),
            items: widget.tables
                .map((t) => DropdownMenuItem(value: t.name, child: Text(t.name)))
                .toList(),
            onChanged: (v) {
              setState(() {
                _selectedSeat = v;
                _tableController.text = v ?? '';
              });
            },
          )
        else
          TextFormField(
            controller: _tableController,
            style: const TextStyle(color: AppTheme.textPrimary),
            decoration: const InputDecoration(
              labelText: 'Eneo / kiti (hiari)',
              hintText: 'Retail',
            ),
          ),
        const SizedBox(height: 12),
        TextFormField(
          controller: _phoneController,
          keyboardType: TextInputType.phone,
          style: const TextStyle(color: AppTheme.textPrimary),
          decoration: const InputDecoration(
            labelText: SalonStrings.labelPhoneOptional,
            hintText: SalonStrings.hintPhoneShort,
          ),
        ),
        const SizedBox(height: 12),
        TextFormField(
          controller: _nameController,
          style: const TextStyle(color: AppTheme.textPrimary),
          decoration: const InputDecoration(
            labelText: SalonStrings.clientNameOptional,
            hintText: SalonStrings.clientNameExampleHint,
          ),
        ),
        const SizedBox(height: 20),
        Text(
          'Njia ya malipo',
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.w700,
            color: AppTheme.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        RadioListTile<bool>(
          value: true,
          groupValue: _payCash,
          onChanged: (v) => setState(() => _payCash = v ?? true),
          title: Text(SalonStrings.payCashInstant,
              style: GoogleFonts.poppins(color: AppTheme.textPrimary)),
        ),
        RadioListTile<bool>(
          value: false,
          groupValue: _payCash,
          onChanged: (v) => setState(() => _payCash = v ?? false),
          title: Text(SalonStrings.payPushSelcom,
              style: GoogleFonts.poppins(color: AppTheme.textPrimary)),
        ),
        if (!_payCash) ...[
          const SizedBox(height: 8),
          TextFormField(
            controller: _pushPhoneController,
            keyboardType: TextInputType.phone,
            style: const TextStyle(color: AppTheme.textPrimary),
            decoration: const InputDecoration(
              labelText: SalonStrings.pushPhoneLabel,
              hintText: SalonStrings.hintPhoneLong,
            ),
          ),
        ],
        const SizedBox(height: 16),
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppTheme.border),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(SalonStrings.total,
                  style: GoogleFonts.poppins(color: AppTheme.textSecondary)),
              Text(
                'TZS ${currency.format(_total)}',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w800,
                  fontSize: 16,
                  color: AppTheme.success,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _footer(NumberFormat currency, bool isTablet) {
    if (isTablet && _step == 0) {
      return const SizedBox.shrink();
    }
    return Container(
      padding: EdgeInsets.fromLTRB(
          16, 12, 16, 12 + MediaQuery.of(context).padding.bottom),
      decoration: const BoxDecoration(
        color: AppTheme.surface,
        border: Border(top: BorderSide(color: AppTheme.border)),
      ),
      child: Row(
        children: [
          if (_step == 1)
            TextButton(
              onPressed: () => setState(() => _step = 0),
              child: Text(SalonStrings.stepBack,
                  style: GoogleFonts.poppins(color: AppTheme.textSecondary)),
            ),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'TZS ${currency.format(_total)}',
                  style: GoogleFonts.poppins(
                    fontWeight: FontWeight.w800,
                    fontSize: 15,
                    color: AppTheme.success,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          if (_step == 0)
            ElevatedButton(
              onPressed: _cart.isEmpty
                  ? null
                  : () => setState(() => _step = 1),
              child: const Text(SalonStrings.stepContinue),
            )
          else
            ElevatedButton(
              onPressed: _loading ? null : _submit,
              child: _loading
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(
                          strokeWidth: 2, color: Colors.white))
                  : Text(_payCash
                      ? SalonStrings.completeProductCash
                      : SalonStrings.continueWithPush),
            ),
        ],
      ),
    );
  }
}
