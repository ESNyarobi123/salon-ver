import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/models.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../utils/booking_time_format.dart';

class NewOrderScreen extends StatefulWidget {
  final List<TableInfo> tables;
  final List<BookingCategory> bookingCategories;
  final VoidCallback onCreated;

  const NewOrderScreen({
    super.key,
    required this.tables,
    required this.bookingCategories,
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

  int _step = 0;
  late DateTime _appointmentDate;
  late TimeOfDay _appointmentTime;
  String? _selectedSeat;

  final Map<int, int> _cart = {};
  bool _isLoading = false;
  String _search = '';

  Iterable<MenuItem> get _allItems sync* {
    for (final c in widget.bookingCategories) {
      yield* c.items;
    }
  }

  double get _totalAmount => _cart.entries.fold(0.0, (sum, entry) {
        MenuItem? found;
        for (final m in _allItems) {
          if (m.id == entry.key) {
            found = m;
            break;
          }
        }
        if (found == null) return sum;
        return sum + found.price * entry.value;
      });

  int get _cartCount => _cart.values.fold(0, (s, v) => s + v);

  @override
  void initState() {
    super.initState();
    final now = DateTime.now();
    _appointmentDate = DateTime(now.year, now.month, now.day);
    _appointmentTime = TimeOfDay.fromDateTime(now);
    if (widget.tables.isNotEmpty) {
      _selectedSeat = widget.tables.first.name;
      _tableController.text = _selectedSeat!;
    }
  }

  @override
  void dispose() {
    _tableController.dispose();
    _phoneController.dispose();
    _nameController.dispose();
    _searchController.dispose();
    super.dispose();
  }

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

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _appointmentDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.dark(
            primary: AppTheme.primary,
            surface: AppTheme.surface,
          ),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => _appointmentDate = picked);
  }

  Future<void> _pickTime() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _appointmentTime,
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.dark(
            primary: AppTheme.primary,
            surface: AppTheme.surface,
          ),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => _appointmentTime = picked);
  }

  bool _validateStep1() {
    if (widget.tables.isNotEmpty) {
      if (_selectedSeat == null || _selectedSeat!.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(SalonStrings.seatRequired,
                style: GoogleFonts.poppins()),
            backgroundColor: AppTheme.error.withOpacity(0.9),
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
          ),
        );
        return false;
      }
      _tableController.text = _selectedSeat!;
      return true;
    }
    if (_tableController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(SalonStrings.seatRequired,
              style: GoogleFonts.poppins()),
          backgroundColor: AppTheme.error.withOpacity(0.9),
          behavior: SnackBarBehavior.floating,
          margin: const EdgeInsets.all(16),
        ),
      );
      return false;
    }
    return true;
  }

  void _goNext() {
    if (_step == 0) {
      setState(() => _step = 1);
      return;
    }
    if (_step == 1) {
      if (!_validateStep1()) return;
      if (widget.bookingCategories.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(SalonStrings.noBookableServices,
                style: GoogleFonts.poppins()),
            backgroundColor: AppTheme.error.withOpacity(0.9),
            behavior: SnackBarBehavior.floating,
            margin: const EdgeInsets.all(16),
          ),
        );
        return;
      }
      setState(() => _step = 2);
    }
  }

  void _goBack() {
    if (_step > 0) setState(() => _step--);
  }

  Future<void> _submit() async {
    if (!_validateStep1()) return;
    if (_cart.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(SalonStrings.newOrderNeedOneItem,
              style: GoogleFonts.poppins()),
          backgroundColor: AppTheme.error.withOpacity(0.9),
          behavior: SnackBarBehavior.floating,
          margin: const EdgeInsets.all(16),
        ),
      );
      return;
    }

    setState(() => _isLoading = true);
    HapticFeedback.lightImpact();

    final dateStr = DateFormat('yyyy-MM-dd').format(_appointmentDate);
    final timeStr = formatApiTime(_appointmentTime);
    final table = widget.tables.isNotEmpty
        ? _selectedSeat!.trim()
        : _tableController.text.trim();

    try {
      await _api.createOrder(
        tableNumber: table,
        scheduledDate: dateStr,
        scheduledTime: timeStr,
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
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12)),
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
            Text(
              SalonStrings.newBookingTitle,
              style: GoogleFonts.poppins(
                fontSize: 17,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
              ),
            ),
            Text(
              SalonStrings.newBookingSubtitleSteps,
              style: GoogleFonts.poppins(
                fontSize: 12,
                color: AppTheme.textSecondary,
              ),
            ),
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
      body: Column(
        children: [
          _buildStepIndicator(),
          Expanded(
            child: isTablet
                ? Row(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Expanded(flex: 3, child: _buildStepBody(currency)),
                      Container(width: 1, color: AppTheme.border),
                      Expanded(flex: 2, child: _buildCartSidebar(currency)),
                    ],
                  )
                : _buildStepBody(currency),
          ),
        ],
      ),
      bottomNavigationBar: isTablet
          ? (_step == 2 ? _buildTabletActions(currency) : _buildStepNav())
          : (_step == 2 && _cart.isNotEmpty
              ? _buildBottomBar(currency)
              : _buildStepNav()),
    );
  }

  Widget _buildStepIndicator() {
    final labels = [
      SalonStrings.stepWhen,
      SalonStrings.stepDetails,
      SalonStrings.stepServices,
    ];
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
      color: AppTheme.surface,
      child: Row(
        children: List.generate(3, (i) {
          final active = _step == i;
          final done = _step > i;
          return Expanded(
            child: Padding(
              padding: EdgeInsets.only(left: i == 0 ? 0 : 6),
              child: Column(
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
                    decoration: BoxDecoration(
                      color: active
                          ? AppTheme.primary.withOpacity(0.15)
                          : done
                              ? AppTheme.success.withOpacity(0.1)
                              : AppTheme.surfaceVariant,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: active
                            ? AppTheme.primary.withOpacity(0.5)
                            : done
                                ? AppTheme.success.withOpacity(0.35)
                                : AppTheme.border,
                      ),
                    ),
                    child: Center(
                      child: Text(
                        labels[i],
                        textAlign: TextAlign.center,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.poppins(
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                          color: active
                              ? AppTheme.primary
                              : done
                                  ? AppTheme.success
                                  : AppTheme.textMuted,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${i + 1}/3',
                    style: GoogleFonts.poppins(
                      fontSize: 9,
                      color: AppTheme.textMuted,
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ),
    );
  }

  Widget _buildStepBody(NumberFormat currency) {
    return IndexedStack(
      index: _step,
      children: [
        _buildStepWhen(),
        _buildStepDetails(),
        _buildStepServices(currency),
      ],
    );
  }

  Widget _buildStepWhen() {
    final df = DateFormat('EEE, d MMM yyyy');
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            SalonStrings.stepWhenTitle,
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w600,
              color: AppTheme.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            SalonStrings.stepWhenHint,
            style: GoogleFonts.poppins(
              fontSize: 13,
              color: AppTheme.textSecondary,
              height: 1.4,
            ),
          ),
          const SizedBox(height: 24),
          Material(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(14),
            child: InkWell(
              onTap: _pickDate,
              borderRadius: BorderRadius.circular(14),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  children: [
                    Icon(Icons.calendar_month_rounded,
                        color: AppTheme.primary.withOpacity(0.9)),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            SalonStrings.labelAppointmentDate,
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              color: AppTheme.textMuted,
                            ),
                          ),
                          Text(
                            df.format(_appointmentDate),
                            style: GoogleFonts.poppins(
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const Icon(Icons.chevron_right_rounded,
                        color: AppTheme.textMuted),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 12),
          Material(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(14),
            child: InkWell(
              onTap: _pickTime,
              borderRadius: BorderRadius.circular(14),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  children: [
                    Icon(Icons.schedule_rounded,
                        color: AppTheme.info.withOpacity(0.9)),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            SalonStrings.labelAppointmentTime,
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              color: AppTheme.textMuted,
                            ),
                          ),
                          Text(
                            formatApiTime(_appointmentTime),
                            style: GoogleFonts.poppins(
                              fontSize: 22,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary,
                              letterSpacing: 1.2,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const Icon(Icons.chevron_right_rounded,
                        color: AppTheme.textMuted),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepDetails() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              SalonStrings.stepDetailsTitle,
              style: GoogleFonts.poppins(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: AppTheme.textPrimary,
              ),
            ),
            const SizedBox(height: 16),
            if (widget.tables.isNotEmpty)
              DropdownButtonFormField<String>(
                initialValue: _selectedSeat,
                dropdownColor: AppTheme.surfaceVariant,
                style: GoogleFonts.poppins(
                    color: AppTheme.textPrimary, fontSize: 14),
                decoration: const InputDecoration(
                  labelText: SalonStrings.labelSeatStar,
                  prefixIcon: Icon(Icons.table_restaurant_outlined,
                      size: 20, color: AppTheme.textSecondary),
                ),
                items: widget.tables
                    .map((t) =>
                        DropdownMenuItem(value: t.name, child: Text(t.name)))
                    .toList(),
                onChanged: (v) => setState(() => _selectedSeat = v),
                validator: (v) => (v == null || v.isEmpty)
                    ? SalonStrings.seatRequired
                    : null,
              )
            else
              TextFormField(
                controller: _tableController,
                style: const TextStyle(
                    color: AppTheme.textPrimary, fontSize: 14),
                decoration: const InputDecoration(
                  labelText: SalonStrings.labelSeatStar,
                  hintText: SalonStrings.seatHint,
                  prefixIcon: Icon(Icons.table_restaurant_outlined,
                      size: 20, color: AppTheme.textSecondary),
                ),
                validator: (v) => (v == null || v.trim().isEmpty)
                    ? SalonStrings.seatRequired
                    : null,
              ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _phoneController,
              keyboardType: TextInputType.phone,
              style: const TextStyle(color: AppTheme.textPrimary, fontSize: 14),
              decoration: const InputDecoration(
                labelText: SalonStrings.labelPhoneOptional,
                hintText: SalonStrings.hintPhoneLong,
                prefixIcon: Icon(Icons.phone_outlined,
                    size: 20, color: AppTheme.textSecondary),
              ),
            ),
            const SizedBox(height: 14),
            TextFormField(
              controller: _nameController,
              style: const TextStyle(color: AppTheme.textPrimary, fontSize: 14),
              decoration: const InputDecoration(
                labelText: SalonStrings.clientNameOptional,
                hintText: SalonStrings.clientNameExampleHint,
                prefixIcon: Icon(Icons.person_outline_rounded,
                    size: 20, color: AppTheme.textSecondary),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStepServices(NumberFormat currency) {
    if (widget.bookingCategories.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Text(
            SalonStrings.noBookableServices,
            textAlign: TextAlign.center,
            style: GoogleFonts.poppins(
              fontSize: 14,
              color: AppTheme.textSecondary,
              height: 1.5,
            ),
          ),
        ),
      );
    }

    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
          child: TextField(
            controller: _searchController,
            style: const TextStyle(color: AppTheme.textPrimary),
            onChanged: (v) => setState(() => _search = v),
            decoration: InputDecoration(
              hintText: SalonStrings.menuSearchServicesHint,
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
            ),
          ),
        ),
        Expanded(
          child: _buildCategorizedServiceList(currency),
        ),
      ],
    );
  }

  Widget _buildCategorizedServiceList(NumberFormat currency) {
    final q = _search.toLowerCase();
    int animIndex = 0;
    final children = <Widget>[];

    for (final cat in widget.bookingCategories) {
      final items =
          cat.items.where((m) => m.name.toLowerCase().contains(q)).toList();
      if (items.isEmpty) continue;

      children.add(
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
          child: Row(
            children: [
              Container(
                width: 3,
                height: 16,
                decoration: BoxDecoration(
                  color: AppTheme.primary,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  cat.name,
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.textPrimary,
                    letterSpacing: 0.2,
                  ),
                ),
              ),
            ],
          ),
        ),
      );

      children.add(
        LayoutBuilder(
          builder: (context, constraints) {
            final cross = constraints.maxWidth > 520 ? 3 : 2;
            return GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: const EdgeInsets.fromLTRB(12, 0, 12, 8),
              gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: cross,
                mainAxisSpacing: 10,
                crossAxisSpacing: 10,
                mainAxisExtent: 172,
              ),
              itemCount: items.length,
              itemBuilder: (_, i) {
                final item = items[i];
                final qty = _cart[item.id] ?? 0;
                final idx = animIndex++;
                return _buildMenuCard(item, qty, currency)
                    .animate()
                    .fadeIn(delay: (idx * 25).ms, duration: 260.ms);
              },
            );
          },
        ),
      );
    }

    if (children.isEmpty) {
      return Center(
        child: Text(
          SalonStrings.menuEmptyState,
          style: GoogleFonts.poppins(color: AppTheme.textMuted),
        ),
      );
    }

    return ListView(
      padding: const EdgeInsets.only(bottom: 24),
      children: children,
    );
  }

  Widget _buildMenuCard(MenuItem item, int qty, NumberFormat currency) {
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
                maxLines: 2,
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
            const Icon(Icons.spa_rounded,
                color: AppTheme.textMuted, size: 28),
            const SizedBox(height: 4),
            Text(
              item.name.length > 10
                  ? '${item.name.substring(0, 10)}…'
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

  Widget _buildCartSidebar(NumberFormat currency) {
    if (_cart.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Text(
            SalonStrings.tapServicesToAdd,
            textAlign: TextAlign.center,
            style: GoogleFonts.poppins(
              fontSize: 13,
              color: AppTheme.textSecondary,
            ),
          ),
        ),
      );
    }
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Text(
          SalonStrings.cart,
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.w600,
            color: AppTheme.textPrimary,
            fontSize: 15,
          ),
        ),
        const SizedBox(height: 10),
        ..._cart.entries.map((entry) {
          MenuItem? m;
          for (final c in widget.bookingCategories) {
            for (final i in c.items) {
              if (i.id == entry.key) {
                m = i;
                break;
              }
            }
            if (m != null) break;
          }
          final name = m?.name ?? SalonStrings.unknownItem;
          return Padding(
            padding: const EdgeInsets.only(bottom: 8),
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    name,
                    style: GoogleFonts.poppins(
                      fontSize: 13,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                ),
                Text(
                  '×${entry.value}',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    color: AppTheme.textSecondary,
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  'TZS ${currency.format((m?.price ?? 0) * entry.value)}',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.primary,
                  ),
                ),
              ],
            ),
          );
        }),
        const Divider(color: AppTheme.border),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              SalonStrings.total,
              style: GoogleFonts.poppins(
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
                fontSize: 15,
              ),
            ),
            Text(
              'TZS ${currency.format(_totalAmount)}',
              style: GoogleFonts.poppins(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppTheme.success,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildTabletActions(NumberFormat currency) {
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
          if (_step > 0)
            TextButton(onPressed: _goBack, child: const Text(SalonStrings.stepBack)),
          const Spacer(),
          ElevatedButton.icon(
            onPressed: _isLoading || _cart.isEmpty ? null : _submit,
            icon: _isLoading
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                        color: Colors.white, strokeWidth: 2))
                : const Icon(Icons.send_rounded),
            label: Text(
              _isLoading ? SalonStrings.submitting : SalonStrings.submitBooking,
              style: GoogleFonts.poppins(fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepNav() {
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
          if (_step > 0)
            TextButton(
              onPressed: _goBack,
              child: const Text(SalonStrings.stepBack),
            ),
          const Spacer(),
          if (_step < 2)
            FilledButton.icon(
              onPressed: () {
                if (_step == 1 && _formKey.currentState != null) {
                  if (!_formKey.currentState!.validate()) return;
                }
                _goNext();
              },
              icon: const Icon(Icons.arrow_forward_rounded, size: 18),
              label: const Text(SalonStrings.stepContinue),
            ),
          if (_step == 2 && _cart.isEmpty)
            Flexible(
              child: Text(
                SalonStrings.chooseServiceToSubmit,
                textAlign: TextAlign.end,
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: AppTheme.textSecondary,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildBottomBar(NumberFormat currency) {
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
          TextButton(onPressed: _goBack, child: const Text(SalonStrings.stepBack)),
          const SizedBox(width: 8),
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
