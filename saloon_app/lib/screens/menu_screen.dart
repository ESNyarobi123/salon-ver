import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';

import '../core/theme.dart';
import '../salon_strings.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import '../widgets/glass_card.dart';
import '../widgets/shimmer_skeletons.dart';

class MenuScreen extends StatefulWidget {
  const MenuScreen({super.key});

  @override
  State<MenuScreen> createState() => _MenuScreenState();
}

class _MenuScreenState extends State<MenuScreen> {
  List<MenuCategory> _categories = [];
  List<MenuItemData> _allItems = [];
  bool _loading = false;
  String _query = '';
  int _selectedCategoryIndex = 0;
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    if (!mounted) return;
    final auth = context.read<AuthProvider>();
    final user = auth.user;

    if (user == null || !user.isLinked) {
      if (mounted) setState(() => _loading = false);
      return;
    }

    setState(() => _loading = true);
    try {
      final api = auth.api;
      final res = await api.getMenu();
      if (mounted) {
        setState(() {
          _categories = res.categories;
          _allItems = res.items;
        });
      }
    } catch (e) {
      debugPrint('Menu load error: $e');
    }
    if (mounted) setState(() => _loading = false);
  }

  List<MenuItemData> get _filteredItems {
    if (_query.isEmpty) {
      if (_categories.isEmpty) return _allItems;
      final cat = _categories[_selectedCategoryIndex];
      return cat.items;
    }
    final q = _query.toLowerCase();
    return _allItems.where((i) => (i.name).toLowerCase().contains(q)).toList();
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final isLinked = user?.isLinked ?? false;

    return Scaffold(
      backgroundColor: AppTheme.surface,
      appBar: AppBar(
        title: const Text(
          SalonStrings.menuCardTitle,
          style: TextStyle(fontWeight: FontWeight.w800),
        ),
      ),
      body: !isLinked
          ? _buildUnlinkedView()
          : _loading
          ? const MenuSkeleton()
          : Column(
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
                  child: TextField(
                    onChanged: (v) => setState(() => _query = v),
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      hintText: SalonStrings.menuSearchHint,
                      hintStyle: TextStyle(
                        color: Colors.white.withValues(alpha: 0.4),
                      ),
                      prefixIcon: Icon(
                        Icons.search_rounded,
                        color: Colors.white.withValues(alpha: 0.6),
                      ),
                      filled: true,
                      fillColor: AppTheme.surfaceLight,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: BorderSide.none,
                      ),
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 18,
                        vertical: 14,
                      ),
                    ),
                  ),
                ),
                if (_categories.isNotEmpty && _query.isEmpty)
                  Container(
                    height: 48,
                    margin: const EdgeInsets.only(bottom: 8),
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      itemCount: _categories.length,
                      itemBuilder: (_, i) {
                        final active = _selectedCategoryIndex == i;
                        return Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 6),
                          child: Material(
                            color: Colors.transparent,
                            child: InkWell(
                              onTap: () =>
                                  setState(() => _selectedCategoryIndex = i),
                              borderRadius: BorderRadius.circular(12),
                              child: AnimatedContainer(
                                duration: const Duration(milliseconds: 200),
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 20,
                                  vertical: 12,
                                ),
                                decoration: BoxDecoration(
                                  gradient: active
                                      ? AppTheme.primaryGradient
                                      : null,
                                  color: active ? null : AppTheme.surfaceLight,
                                  borderRadius: BorderRadius.circular(12),
                                  border: active
                                      ? null
                                      : Border.all(color: AppTheme.glassBorder),
                                ),
                                child: Center(
                                  child: Text(
                                    _categories[i].name,
                                    style: TextStyle(
                                      fontWeight: FontWeight.w600,
                                      color: active
                                          ? Colors.white
                                          : Colors.white70,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                Expanded(
                  child: _filteredItems.isEmpty
                      ? Center(
                          child: Text(
                            SalonStrings.menuNoItems,
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.5),
                              fontSize: 16,
                            ),
                          ),
                        )
                      : ListView.builder(
                          controller: _scrollController,
                          padding: const EdgeInsets.symmetric(
                            horizontal: 16,
                            vertical: 8,
                          ),
                          itemCount: _filteredItems.length,
                          itemBuilder: (_, i) {
                            final item = _filteredItems[i];
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 12),
                              child: _buildMenuItem(item),
                            );
                          },
                        ),
                ),
              ],
            ),
    );
  }

  Widget _buildUnlinkedView() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: LinearGradient(
                  colors: [
                    const Color(0xFFD97706).withValues(alpha: 0.3),
                    const Color(0xFFF59E0B).withValues(alpha: 0.2),
                  ],
                ),
              ),
              child: Icon(
                Icons.restaurant_menu_rounded,
                size: 50,
                color: const Color(0xFFD97706).withValues(alpha: 0.8),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              SalonStrings.menuNotLinkedTitle,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: Colors.white.withValues(alpha: 0.9),
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 12),
            Text(
              SalonStrings.menuNotLinkedBody,
              style: TextStyle(
                fontSize: 14,
                color: Colors.white.withValues(alpha: 0.6),
                height: 1.5,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.surfaceLight,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: const Color(0xFFD97706).withValues(alpha: 0.3),
                ),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.info_outline_rounded,
                    color: const Color(0xFFD97706),
                    size: 20,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      SalonStrings.staffNumberLine(
                        context.read<AuthProvider>().user?.globalWaiterNumber ??
                            SalonStrings.staffNumberUnavailable,
                      ),
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors.white.withValues(alpha: 0.7),
                        fontWeight: FontWeight.w500,
                      ),
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

  Widget _buildMenuItem(MenuItemData item) {
    final available = item.isAvailable;
    final initials = item.name.length >= 2
        ? item.name.substring(0, 2).toUpperCase()
        : item.name.toUpperCase();

    return GlassCard(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              gradient: available ? AppTheme.primaryGradient : null,
              color: available ? null : Colors.grey.withValues(alpha: 0.3),
              borderRadius: BorderRadius.circular(14),
            ),
            alignment: Alignment.center,
            child: item.image != null && item.image!.isNotEmpty
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(14),
                    child: Image.network(
                      item.image!,
                      width: 64,
                      height: 64,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Text(
                        initials,
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.8),
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  )
                : Text(
                    initials,
                    style: TextStyle(
                      color: available ? Colors.white : Colors.white54,
                      fontWeight: FontWeight.w800,
                      fontSize: 16,
                    ),
                  ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        item.name,
                        style: TextStyle(
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                          fontSize: 15,
                          decoration: available
                              ? null
                              : TextDecoration.lineThrough,
                          decorationColor: AppTheme.error,
                        ),
                      ),
                    ),
                    Text(
                      'Tsh ${NumberFormat('#,###').format(item.price)}',
                      style: const TextStyle(
                        color: AppTheme.primary,
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ),
                if (item.description != null && item.description!.isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Text(
                      item.description!,
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.6),
                        fontSize: 12,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: available
                            ? AppTheme.success.withValues(alpha: 0.2)
                            : Colors.grey.withValues(alpha: 0.3),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        available
                            ? SalonStrings.menuItemAvailable
                            : SalonStrings.menuItemSoldOut,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          color: available ? AppTheme.success : Colors.white54,
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Text(
                      '${item.preparationTime} min',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.5),
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
