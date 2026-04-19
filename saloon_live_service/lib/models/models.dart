class OrderItem {
  final int id;
  final int menuItemId;
  final String name;
  final int quantity;
  final double price;
  final double total;

  OrderItem({
    required this.id,
    required this.menuItemId,
    required this.name,
    required this.quantity,
    required this.price,
    required this.total,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) => OrderItem(
        id: json['id'] ?? 0,
        menuItemId: json['menu_item_id'] ?? 0,
        name: json['name'] ?? '',
        quantity: json['quantity'] ?? 0,
        price: double.tryParse(json['price'].toString()) ?? 0,
        total: double.tryParse(json['total'].toString()) ?? 0,
      );
}

class Order {
  final int id;
  final String tableNumber;
  final String? customerPhone;
  final String? customerName;
  final DateTime? scheduledAt;
  final double totalAmount;
  final String status;
  final DateTime createdAt;
  final List<OrderItem> items;

  Order({
    required this.id,
    required this.tableNumber,
    this.customerPhone,
    this.customerName,
    this.scheduledAt,
    required this.totalAmount,
    required this.status,
    required this.createdAt,
    required this.items,
  });

  factory Order.fromJson(Map<String, dynamic> json) => Order(
        id: json['id'] ?? 0,
        tableNumber: json['table_number'] ?? '',
        customerPhone: json['customer_phone'],
        customerName: json['customer_name'],
        scheduledAt: json['scheduled_at'] != null
            ? DateTime.tryParse(json['scheduled_at'].toString())
            : null,
        totalAmount: double.tryParse(json['total_amount'].toString()) ?? 0,
        status: json['status'] ?? 'pending',
        createdAt:
            DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
        items: (json['items'] as List? ?? [])
            .map((e) => OrderItem.fromJson(e))
            .toList(),
      );

  Order copyWith({String? status}) => Order(
        id: id,
        tableNumber: tableNumber,
        customerPhone: customerPhone,
        customerName: customerName,
        scheduledAt: scheduledAt,
        totalAmount: totalAmount,
        status: status ?? this.status,
        createdAt: createdAt,
        items: items,
      );
}

class TableInfo {
  final int id;
  final String name;

  TableInfo({required this.id, required this.name});

  factory TableInfo.fromJson(Map<String, dynamic> json) =>
      TableInfo(id: json['id'] ?? 0, name: json['name'] ?? '');
}

class MenuItem {
  final int id;
  final String name;
  final double price;
  final String? imageUrl;

  MenuItem({
    required this.id,
    required this.name,
    required this.price,
    this.imageUrl,
  });

  factory MenuItem.fromJson(Map<String, dynamic> json) => MenuItem(
        id: json['id'] ?? 0,
        name: json['name'] ?? '',
        price: double.tryParse(json['price'].toString()) ?? 0,
        imageUrl: json['image_url'],
      );
}

/// Service catalog group (matches API `meta.booking_categories`).
class BookingCategory {
  final int id;
  final String name;
  final List<MenuItem> items;

  BookingCategory({
    required this.id,
    required this.name,
    required this.items,
  });

  factory BookingCategory.fromJson(Map<String, dynamic> json) =>
      BookingCategory(
        id: json['id'] ?? 0,
        name: json['name'] ?? '',
        items: (json['items'] as List? ?? [])
            .map((e) => MenuItem.fromJson(e as Map<String, dynamic>))
            .toList(),
      );
}

class Restaurant {
  final int id;
  final String name;

  Restaurant({required this.id, required this.name});

  factory Restaurant.fromJson(Map<String, dynamic> json) =>
      Restaurant(id: json['id'] ?? 0, name: json['name'] ?? '');
}

class OrdersData {
  final List<Order> pending;
  final List<Order> preparing;
  final List<Order> served;
  final List<Order> paid;
  final List<TableInfo> tables;
  final List<MenuItem> menuItems;
  final List<BookingCategory> bookingCategories;
  final Restaurant? restaurant;

  OrdersData({
    required this.pending,
    required this.preparing,
    required this.served,
    required this.paid,
    required this.tables,
    required this.menuItems,
    required this.bookingCategories,
    this.restaurant,
  });

  factory OrdersData.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    final meta = json['meta'] as Map<String, dynamic>? ?? {};
    final menuItems = (meta['menu_items'] as List? ?? [])
        .map((e) => MenuItem.fromJson(e as Map<String, dynamic>))
        .toList();
    List<BookingCategory> categories = (meta['booking_categories'] as List? ??
            [])
        .map((e) => BookingCategory.fromJson(e as Map<String, dynamic>))
        .toList();
    if (categories.isEmpty && menuItems.isNotEmpty) {
      categories = [
        BookingCategory(id: 0, name: 'Huduma', items: menuItems),
      ];
    }
    return OrdersData(
      pending: (data['pending'] as List? ?? [])
          .map((e) => Order.fromJson(e as Map<String, dynamic>))
          .toList(),
      preparing: (data['preparing'] as List? ?? [])
          .map((e) => Order.fromJson(e as Map<String, dynamic>))
          .toList(),
      served: (data['served'] as List? ?? [])
          .map((e) => Order.fromJson(e as Map<String, dynamic>))
          .toList(),
      paid:
          (data['paid'] as List? ?? []).map((e) => Order.fromJson(e as Map<String, dynamic>)).toList(),
      tables: (meta['tables'] as List? ?? [])
          .map((e) => TableInfo.fromJson(e as Map<String, dynamic>))
          .toList(),
      menuItems: menuItems,
      bookingCategories: categories,
      restaurant: meta['restaurant'] != null
          ? Restaurant.fromJson(meta['restaurant'] as Map<String, dynamic>)
          : null,
    );
  }

  List<Order> get allOrders => [...pending, ...preparing, ...served, ...paid];

  int get totalActive => pending.length + preparing.length + served.length;
}

class AuthData {
  final int restaurantId;
  final String restaurantName;
  final int userId;
  final String userName;
  final String token;

  AuthData({
    required this.restaurantId,
    required this.restaurantName,
    required this.userId,
    required this.userName,
    required this.token,
  });

  factory AuthData.fromJson(Map<String, dynamic> json) => AuthData(
        restaurantId: json['restaurant_id'] ?? 0,
        restaurantName: json['restaurant_name'] ?? '',
        userId: json['user_id'] ?? 0,
        userName: json['user_name'] ?? '',
        token: json['token'] ?? '',
      );

  Map<String, dynamic> toJson() => {
        'restaurant_id': restaurantId,
        'restaurant_name': restaurantName,
        'user_id': userId,
        'user_name': userName,
        'token': token,
      };
}
