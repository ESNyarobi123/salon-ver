import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/models.dart';
import '../salon_strings.dart';

class ApiService {
  /// Override for dev: `flutter run --dart-define=ORDER_PORTAL_API_BASE=http://127.0.0.1:8000/api/order-portal`
  static const String baseUrl = String.fromEnvironment(
    'ORDER_PORTAL_API_BASE',
    defaultValue: 'https://tiptapafrica.co.tz/api/order-portal',
  );
  static const String _authDataKey = 'auth_data';

  AuthData? _cachedAuth;

  // Singleton
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  // ─── Auth Data ────────────────────────────────────────────────────────────

  Future<void> saveAuthData(AuthData data) async {
    _cachedAuth = data;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_authDataKey, jsonEncode(data.toJson()));
  }

  Future<AuthData?> getAuthData() async {
    if (_cachedAuth != null) return _cachedAuth;
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString(_authDataKey);
    if (raw == null) return null;
    _cachedAuth = AuthData.fromJson(jsonDecode(raw));
    return _cachedAuth;
  }

  Future<void> _clearAuth() async {
    _cachedAuth = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_authDataKey);
  }

  // ─── Headers ─────────────────────────────────────────────────────────────

  Future<Map<String, String>> _headers({bool withAuth = false}) async {
    final Map<String, String> headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    if (withAuth) {
      final auth = await getAuthData();
      if (auth != null && auth.token.isNotEmpty) {
        headers['Authorization'] = 'Bearer ${auth.token}';
      }
    }
    return headers;
  }

  // ─── Login ───────────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> login(String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: await _headers(),
      body: jsonEncode({'password': password}),
    );

    final body = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode == 200 && body['success'] == true) {
      final authData = AuthData.fromJson(body['data'] as Map<String, dynamic>);
      await saveAuthData(authData);
      return {'success': true, 'data': authData};
    }

    return {
      'success': false,
      'message': body['message'] ?? SalonStrings.loginFailed,
    };
  }

  // ─── Logout ──────────────────────────────────────────────────────────────

  Future<void> logout() async {
    try {
      await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: await _headers(withAuth: true),
      );
    } catch (_) {}
    await _clearAuth();
  }

  // ─── Orders ──────────────────────────────────────────────────────────────

  Future<OrdersData> getOrders() async {
    final response = await http.get(
      Uri.parse('$baseUrl/orders'),
      headers: await _headers(withAuth: true),
    );

    if (response.statusCode == 401) {
      throw UnauthorizedException();
    }

    if (response.statusCode == 200) {
      return OrdersData.fromJson(jsonDecode(response.body));
    }

    throw ApiException(
        '${SalonStrings.apiLoadBookingsFailed} (${response.statusCode})');
  }

  Future<Order> createOrder({
    required String tableNumber,
    String? customerPhone,
    String? customerName,
    required List<Map<String, int>> items,
  }) async {
    final body = <String, dynamic>{
      'table_number': tableNumber,
      'items': items,
    };
    if (customerPhone != null && customerPhone.isNotEmpty) {
      body['customer_phone'] = customerPhone;
    }
    if (customerName != null && customerName.isNotEmpty) {
      body['customer_name'] = customerName;
    }

    final response = await http.post(
      Uri.parse('$baseUrl/orders'),
      headers: await _headers(withAuth: true),
      body: jsonEncode(body),
    );

    if (response.statusCode == 401) throw UnauthorizedException();

    final responseBody = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode == 201) {
      return Order.fromJson(responseBody['data'] as Map<String, dynamic>);
    }

    final message =
        responseBody['message'] ?? SalonStrings.apiCreateBookingFailed;
    throw ApiException(message);
  }

  Future<Order> updateOrderStatus(int orderId, String status) async {
    final response = await http.put(
      Uri.parse('$baseUrl/orders/$orderId'),
      headers: await _headers(withAuth: true),
      body: jsonEncode({'status': status}),
    );

    if (response.statusCode == 401) throw UnauthorizedException();

    final responseBody = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode == 200) {
      return Order.fromJson(responseBody['data'] as Map<String, dynamic>);
    }

    throw ApiException(
        responseBody['message'] ?? SalonStrings.apiUpdateBookingFailed);
  }

  Future<Order> updateOrder(int orderId, Map<String, dynamic> data) async {
    final response = await http.put(
      Uri.parse('$baseUrl/orders/$orderId'),
      headers: await _headers(withAuth: true),
      body: jsonEncode(data),
    );

    if (response.statusCode == 401) throw UnauthorizedException();

    final responseBody = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode == 200) {
      return Order.fromJson(responseBody['data'] as Map<String, dynamic>);
    }

    throw ApiException(
        responseBody['message'] ?? SalonStrings.apiUpdateBookingFailed);
  }

  Future<void> deleteOrder(int orderId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/orders/$orderId'),
      headers: await _headers(withAuth: true),
    );

    if (response.statusCode == 401) throw UnauthorizedException();
    if (response.statusCode != 200) {
      throw ApiException(SalonStrings.apiDeleteBookingFailed);
    }
  }

  // ─── Payments ────────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> initiatePayment({
    required int orderId,
    required String phone,
    String? name,
  }) async {
    final body = <String, dynamic>{
      'order_id': orderId,
      'phone': phone,
    };
    if (name != null && name.isNotEmpty) {
      body['name'] = name;
    }

    final response = await http.post(
      Uri.parse('$baseUrl/payments/selcom/initiate'),
      headers: await _headers(withAuth: true),
      body: jsonEncode(body),
    );

    if (response.statusCode == 401) throw UnauthorizedException();

    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getPaymentStatus(int orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/payments/selcom/status/$orderId'),
      headers: await _headers(withAuth: true),
    );

    if (response.statusCode == 401) throw UnauthorizedException();

    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  // ─── Auth Check ─────────────────────────────────────────────────────────

  Future<bool> isLoggedIn() async {
    final auth = await getAuthData();
    if (auth == null || auth.token.isEmpty) return false;
    return true;
  }
}

class UnauthorizedException implements Exception {
  final String message;
  UnauthorizedException([this.message = SalonStrings.sessionExpired]);
  @override
  String toString() => message;
}

class ApiException implements Exception {
  final String message;
  ApiException(this.message);
  @override
  String toString() => message;
}
