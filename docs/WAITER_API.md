# TIPTAP Waiter API Documentation

Base URL: `https://sln.tiptapafrica.co.tz/api`

Auth: `Authorization: Bearer {token}` (from `/api/auth/login`)

Role: All endpoints require `waiter` role.

---

## Auth (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/login` | Login, returns token + user |
| POST | `/auth/logout` | Logout, revoke token |

---

## Waiter Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/waiter/dashboard` | Full dashboard data (stats, orders, requests, feedback) |
| GET | `/waiter/dashboard/stats` | Quick stats only (for polling) |

---

## Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/waiter/orders` | List my orders (paginated, 15 per page) |
| POST | `/waiter/orders/{order}/claim` | Claim an unassigned order |

---

## Customer Calls (Call Waiter / Request Bill)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/waiter/requests` | List pending customer requests |
| POST | `/waiter/requests/{customerRequest}/complete` | Mark request as attended |

---

## Tips & Ratings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/waiter/tips` | List my tips (paginated) |
| GET | `/waiter/ratings` | List my feedback/ratings (paginated) |

---

## Menu

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/waiter/menu` | Restaurant menu (categories + items) |

---

## Request/Response Examples

### POST /auth/login
```json
// Request
{ "email": "waiter@example.com", "password": "..." }

// Response 200
{
  "success": true,
  "token": "1|xxx",
  "user": {
    "id": 13,
    "name": "...",
    "restaurant_name": "Samaki Samaki",
    "waiter_code": "SAM-W02",
    "waiter_qr_url": "https://wa.me/255794321510?text=START_2_W13"
  }
}
```

### GET /waiter/dashboard
```json
// Response 200
{
  "success": true,
  "data": {
    "stats": { "tips_today": 0, "my_active_orders": 2, ... },
    "unassigned_orders": [...],
    "pending_requests": [...],
    "recent_feedback": [...],
    "my_orders_today": [...]
  }
}
```

### POST /waiter/orders/{id}/claim
```json
// Response 200
{ "success": true, "message": "Order #45 is now assigned to you!", "data": { "order_id": 45 } }

// Response 422 - Already claimed
{ "success": false, "message": "This order has already been claimed by another waiter." }
```

### POST /waiter/requests/{id}/complete
```json
// Response 200
{ "success": true, "message": "Request marked as attended!", "data": { "request_id": 1 } }
```

---

## Cash payment & change notification (v1 API)

Use these with the same `Authorization: Bearer {token}` (waiter or manager). Base path: `/api/v1`.

### POST /payments/cash/change-notification (before payment)

Get the change to give to the customer **before** confirming cash payment. Show this in the app so the waiter knows how much to give back.

**Request**
```json
{ "order_id": 45, "amount_received": 10000 }
```

**Response 200**
```json
{
  "success": true,
  "order_id": 45,
  "order_total": 9500,
  "amount_received": 10000,
  "change_to_give": 500,
  "message": "Change to give to customer: 500 Tsh"
}
```

Then call `POST /payments/cash` to record the payment.

### POST /payments/cash

Record a cash payment. Optionally send `amount_received`; response will include `change_to_give` for receipt/notification.

**Request**
```json
{ "order_id": 45 }
```
or
```json
{ "order_id": 45, "amount_received": 10000 }
```

**Response 200**
```json
{
  "success": true,
  "payment": { "id": 1, "order_id": 45, "amount": 9500, "method": "cash", "status": "paid", ... }
}
```
When `amount_received` was sent:
```json
{
  "success": true,
  "payment": { ... },
  "change_to_give": 500,
  "order_total": 9500,
  "amount_received": 10000,
  "message": "Change to give to customer: 500 Tsh"
}
```
