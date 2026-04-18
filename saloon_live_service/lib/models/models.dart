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
  final double totalAmount;
  final String status;
  final DateTime createdAt;
  final List<OrderItem> items;

  Order({
    required this.id,
    required this.tableNumber,
    this.customerPhone,
    this.customerName,
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
  final Restaurant? restaurant;

  OrdersData({
    required this.pending,
    required this.preparing,
    required this.served,
    required this.paid,
    required this.tables,
    required this.menuItems,
    this.restaurant,
  });

  factory OrdersData.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    final meta = json['meta'] as Map<String, dynamic>? ?? {};
    return OrdersData(
      pending: (data['pending'] as List? ?? [])
          .map((e) => Order.fromJson(e))
          .toList(),
      preparing: (data['preparing'] as List? ?? [])
          .map((e) => Order.fromJson(e))
          .toList(),
      served: (data['served'] as List? ?? [])
          .map((e) => Order.fromJson(e))
          .toList(),
      paid:
          (data['paid'] as List? ?? []).map((e) => Order.fromJson(e)).toList(),
      tables: (meta['tables'] as List? ?? [])
          .map((e) => TableInfo.fromJson(e))
          .toList(),
      menuItems: (meta['menu_items'] as List? ?? [])
          .map((e) => MenuItem.fromJson(e))
          .toList(),
      restaurant: meta['restaurant'] != null
          ? Restaurant.fromJson(meta['restaurant'])
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
