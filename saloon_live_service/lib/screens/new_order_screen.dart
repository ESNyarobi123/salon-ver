import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';

class NewOrderScreen extends StatefulWidget {
  final List<TableInfo> tables;
  final List<MenuItem> menuItems;
  final VoidCallback onCreated;

  const NewOrderScreen({
    super.key,
    required this.tables,
    required this.menuItems,
    required this.onCreated,
  });

  @override
  State<NewOrderScreen> createState() => _NewOrderScreenState();
}

class _NewOrderScreenState extends State<NewOrderScreen> {
  final _api = ApiService();
  final _formKey = GlobalKey<FormState>();
  final _tableController = TextEditingController();
  final _phoneController = TextEditingController();
  final _nameController = TextEditingController();
  final _searchController = TextEditingController();

  final Map<int, int> _cart = {}; // menuItemId -> quantity
  bool _isLoading = false;
  String _search = '';

  @override
  void dispose() {
    _tableController.dispose();
    _phoneController.dispose();
    _nameController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  List<MenuItem> get _filteredItems => widget.menuItems
      .where((m) => m.name.toLowerCase().contains(_search.toLowerCase()))
      .toList();

  double get _totalAmount => _cart.entries.fold(0, (sum, entry) {
        final item = widget.menuItems.firstWhere(
          (m) => m.id == entry.key,
          orElse: () => MenuItem(id: 0, name: '', price: 0),
        );
        return sum + item.price * entry.value;
      });

  int get _cartCount => _cart.values.fold(0, (s, v) => s + v);

  void _addToCart(MenuItem item) {
    HapticFeedback.selectionClick();
    setState(() => _cart[item.id] = (_cart[item.id] ?? 0) + 1);
  }

  void _removeFromCart(MenuItem item) {
    HapticFeedback.selectionClick();
    setState(() {
      if ((_cart[item.id] ?? 0) <= 1) {
        _cart.remove(item.id);
      } else {
        _cart[item.id] = (_cart[item.id]! - 1);
      }
    });
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_cart.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(SalonStrings.newOrderNeedOneItem,
              style: GoogleFonts.poppins()),
          backgroundColor: AppTheme.error.withOpacity(0.9),
          behavior: SnackBarBehavior.floating,
          margin: const EdgeInsets.all(16),
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
      return;
    }

    setState(() => _isLoading = true);
    HapticFeedback.lightImpact();

    try {
      await _api.createOrder(
        tableNumber: _tableController.text.trim(),
        customerPhone: _phoneController.text.trim(),
        customerName: _nameController.text.trim(),
        items: _cart.entries
            .map((e) => {'id': e.key, 'quantity': e.value})
            .toList(),
      );

      widget.onCreated();
      if (mounted) {
        HapticFeedback.heavyImpact();
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(SalonStrings.newOrderCreatedSuccess,
                style: GoogleFonts.poppins()),
            backgroundColor: AppTheme.success.withOpacity(0.9),
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString(), style: GoogleFonts.poppins()),
            backgroundColor: AppTheme.error.withOpacity(0.9),
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
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
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(SalonStrings.newBookingTitle,
                style: GoogleFonts.poppins(
                    fontSize: 17,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.textPrimary)),
            Text(SalonStrings.newBookingSubtitle,
                style: GoogleFonts.poppins(
                    fontSize: 12, color: AppTheme.textSecondary)),
          ],
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          color: AppTheme.textPrimary,
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          if (_cartCount > 0)
            Container(
              margin: const EdgeInsets.only(right: 16),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: AppTheme.primary.withOpacity(0.15),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.primary.withOpacity(0.4)),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.shopping_cart_rounded,
                      color: AppTheme.primary, size: 16),
                  const SizedBox(width: 4),
                  Text(
                    SalonStrings.cartSummary(
                        _cartCount, currency.format(_totalAmount)),
                    style: GoogleFonts.poppins(
                      color: AppTheme.primary,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppTheme.border),
        ),
      ),
      body:
          isTablet ? _buildTabletLayout(currency) : _buildPhoneLayout(currency),
      bottomNavigationBar: _buildBottomBar(currency),
    );
  }

  Widget _buildTabletLayout(NumberFormat currency) {
    return Row(
      children: [
        // Left: Menu
        Expanded(
          flex: 3,
          child: _buildMenuSection(),
        ),
        Container(width: 1, color: AppTheme.border),
        // Right: Form + Cart
        Expanded(
          flex: 2,
          child: _buildFormAndCart(currency),
        ),
      ],
    );
  }

  Widget _buildPhoneLayout(NumberFormat currency) {
    return Column(
      children: [
        // Details form (collapsed)
        _buildOrderDetailsForm(),
        Expanded(child: _buildMenuSection()),
      ],
    );
  }

  Widget _buildOrderDetailsForm() {
    return Container(
      color: AppTheme.surface,
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
      child: Form(
        key: _formKey,
        child: Row(
          children: [
            Expanded(
              child: _buildTableField(),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: TextFormField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                style:
                    const TextStyle(color: AppTheme.textPrimary, fontSize: 14),
                decoration: const InputDecoration(
                  labelText: SalonStrings.labelPhoneOptional,
                  hintText: SalonStrings.hintPhoneShort,
                  prefixIcon: Icon(Icons.phone_outlined,
                      size: 18, color: AppTheme.textSecondary),
                  contentPadding:
                      EdgeInsets.symmetric(horizontal: 12, vertical: 14),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTableField() {
    if (widget.tables.isNotEmpty) {
      return DropdownButtonFormField<String>(
        initialValue: _tableController.text.isEmpty ? null : _tableController.text,
        dropdownColor: AppTheme.surfaceVariant,
        style: GoogleFonts.poppins(color: AppTheme.textPrimary, fontSize: 14),
        decoration: const InputDecoration(
          labelText: SalonStrings.labelSeatStar,
          prefixIcon: Icon(Icons.table_restaurant_outlined,
              size: 18, color: AppTheme.textSecondary),
          fillColor: AppTheme.surfaceVariant,
          contentPadding:
              EdgeInsets.symmetric(horizontal: 12, vertical: 14),
        ),
        items: widget.tables
            .map((t) => DropdownMenuItem(value: t.name, child: Text(t.name)))
            .toList(),
        onChanged: (v) {
          if (v != null) _tableController.text = v;
        },
        validator: (v) =>
            (v == null || v.isEmpty) ? SalonStrings.seatRequired : null,
      );
    }

    return TextFormField(
      controller: _tableController,
      style: const TextStyle(color: AppTheme.textPrimary, fontSize: 14),
      decoration: const InputDecoration(
        labelText: SalonStrings.labelSeatStar,
        hintText: SalonStrings.seatHint,
        prefixIcon: Icon(Icons.table_restaurant_outlined,
            size: 18, color: AppTheme.textSecondary),
        contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 14),
      ),
      validator: (v) => (v == null || v.trim().isEmpty)
          ? SalonStrings.seatRequired
          : null,
    );
  }

  Widget _buildMenuSection() {
    return Column(
      children: [
        // Search
        Container(
          color: AppTheme.bg,
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
          child: TextField(
            controller: _searchController,
            style: const TextStyle(color: AppTheme.textPrimary),
            onChanged: (v) => setState(() => _search = v),
            decoration: InputDecoration(
              hintText: SalonStrings.menuSearchHintShort,
              prefixIcon: const Icon(Icons.search_rounded,
                  color: AppTheme.textSecondary),
              suffixIcon: _search.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.clear_rounded,
                          color: AppTheme.textSecondary),
                      onPressed: () {
                        _searchController.clear();
                        setState(() => _search = '');
                      },
                    )
                  : null,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            ),
          ),
        ),

        Expanded(
          child: _filteredItems.isEmpty
              ? Center(
                  child: Text(SalonStrings.menuEmptyState,
                      style: GoogleFonts.poppins(color: AppTheme.textMuted)),
                )
              : GridView.builder(
                  padding: const EdgeInsets.fromLTRB(12, 4, 12, 12),
                  gridDelegate: const SliverGridDelegateWithMaxCrossAxisExtent(
                    maxCrossAxisExtent: 180,
                    mainAxisSpacing: 10,
                    crossAxisSpacing: 10,
                    mainAxisExtent: 170,
                  ),
                  itemCount: _filteredItems.length,
                  itemBuilder: (_, i) {
                    final item = _filteredItems[i];
                    final qty = _cart[item.id] ?? 0;
                    return _buildMenuCard(item, qty)
                        .animate()
                        .fadeIn(delay: (i * 40).ms, duration: 300.ms)
                        .slideY(begin: 0.1);
                  },
                ),
        ),
      ],
    );
  }

  Widget _buildMenuCard(MenuItem item, int qty) {
    final currency = NumberFormat('#,##0', 'en_US');
    return GestureDetector(
      onTap: () => _addToCart(item),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        decoration: BoxDecoration(
          color:
              qty > 0 ? AppTheme.primary.withOpacity(0.08) : AppTheme.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: qty > 0 ? AppTheme.primary : AppTheme.border,
            width: qty > 0 ? 1.5 : 1,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image / placeholder
            Expanded(
              child: ClipRRect(
                borderRadius:
                    const BorderRadius.vertical(top: Radius.circular(13)),
                child: item.imageUrl != null
                    ? Image.network(
                        item.imageUrl!,
                        fit: BoxFit.cover,
                        width: double.infinity,
                        errorBuilder: (_, __, ___) => _menuPlaceholder(item),
                      )
                    : _menuPlaceholder(item),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(10, 8, 10, 4),
              child: Text(
                item.name,
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: AppTheme.textPrimary,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(10, 0, 10, 10),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      'TZS ${currency.format(item.price)}',
                      style: GoogleFonts.poppins(
                        fontSize: 11,
                        color: AppTheme.primary,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  if (qty > 0) ...[
                    GestureDetector(
                      onTap: () => _removeFromCart(item),
                      child: Container(
                        width: 22,
                        height: 22,
                        decoration: BoxDecoration(
                          color: AppTheme.error.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: const Icon(Icons.remove_rounded,
                            size: 14, color: AppTheme.error),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 6),
                      child: Text(
                        '$qty',
                        style: GoogleFonts.poppins(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.primary,
                        ),
                      ),
                    ),
                  ],
                  GestureDetector(
                    onTap: () => _addToCart(item),
                    child: Container(
                      width: 22,
                      height: 22,
                      decoration: BoxDecoration(
                        color: AppTheme.primary.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: const Icon(Icons.add_rounded,
                          size: 14, color: AppTheme.primary),
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

  Widget _menuPlaceholder(MenuItem item) {
    return Container(
      color: AppTheme.surfaceVariant,
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.restaurant_rounded,
                color: AppTheme.textMuted, size: 28),
            const SizedBox(height: 4),
            Text(
              item.name.length > 8
                  ? '${item.name.substring(0, 8)}...'
                  : item.name,
              style:
                  GoogleFonts.poppins(fontSize: 9, color: AppTheme.textMuted),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFormAndCart(NumberFormat currency) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(SalonStrings.bookingDetails,
                style: GoogleFonts.poppins(
                    fontWeight: FontWeight.w600,
                    color: AppTheme.textPrimary,
                    fontSize: 15)),
            const SizedBox(height: 12),
            _buildTableField(),
            const SizedBox(height: 12),
            TextFormField(
              controller: _phoneController,
              keyboardType: TextInputType.phone,
              style: const TextStyle(color: AppTheme.textPrimary),
              decoration: const InputDecoration(
                labelText: SalonStrings.labelPhoneOptional,
                hintText: SalonStrings.hintPhoneLong,
                prefixIcon:
                    Icon(Icons.phone_outlined, color: AppTheme.textSecondary),
              ),
            ),
            const SizedBox(height: 12),
            TextFormField(
              controller: _nameController,
              style: const TextStyle(color: AppTheme.textPrimary),
              decoration: const InputDecoration(
                labelText: SalonStrings.clientNameOptional,
                hintText: SalonStrings.clientNameExampleHint,
                prefixIcon: Icon(Icons.person_outline_rounded,
                    color: AppTheme.textSecondary),
              ),
            ),
            if (_cart.isNotEmpty) ...[
              const SizedBox(height: 20),
              Text(SalonStrings.cart,
                  style: GoogleFonts.poppins(
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textPrimary,
                      fontSize: 15)),
              const SizedBox(height: 8),
              ..._cart.entries.map((entry) {
                final m = widget.menuItems.firstWhere((m) => m.id == entry.key,
                    orElse: () =>
                        MenuItem(id: 0, name: SalonStrings.unknownItem, price: 0));
                return Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(m.name,
                            style: GoogleFonts.poppins(
                                fontSize: 13, color: AppTheme.textPrimary)),
                      ),
                      Text('x${entry.value}',
                          style: GoogleFonts.poppins(
                              fontSize: 13, color: AppTheme.textSecondary)),
                      const SizedBox(width: 8),
                      Text('TZS ${currency.format(m.price * entry.value)}',
                          style: GoogleFonts.poppins(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.primary)),
                    ],
                  ),
                );
              }),
              const Divider(color: AppTheme.border),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(SalonStrings.total,
                      style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w700,
                          color: AppTheme.textPrimary,
                          fontSize: 15)),
                  Text(
                    'TZS ${currency.format(_totalAmount)}',
                    style: GoogleFonts.poppins(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.success),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildBottomBar(NumberFormat currency) {
    if (_cart.isEmpty) return const SizedBox.shrink();

    return Container(
      padding: EdgeInsets.fromLTRB(
        20,
        12,
        20,
        MediaQuery.of(context).padding.bottom + 12,
      ),
      decoration: const BoxDecoration(
        color: AppTheme.surface,
        border: Border(top: BorderSide(color: AppTheme.border)),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  SalonStrings.linesCount(_cartCount),
                  style: GoogleFonts.poppins(
                      fontSize: 12, color: AppTheme.textSecondary),
                ),
                Text(
                  'TZS ${currency.format(_totalAmount)}',
                  style: GoogleFonts.poppins(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.success,
                  ),
                ),
              ],
            ),
          ),
          SizedBox(
            height: 48,
            child: ElevatedButton.icon(
              onPressed: _isLoading ? null : _submit,
              icon: _isLoading
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                          color: Colors.white, strokeWidth: 2))
                  : const Icon(Icons.send_rounded),
              label: Text(
                _isLoading
                    ? SalonStrings.submitting
                    : SalonStrings.submitBooking,
                style: GoogleFonts.poppins(fontWeight: FontWeight.w600),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
