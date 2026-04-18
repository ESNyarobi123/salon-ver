---
description: oder portal 
---

# TIPTAP Order Portal API

API za Order Portal (waiter anaingia kwa password ya manager).

**Base URL (pick one):**
- **Web:** `{APP_URL}/order-portal` (mfano: `https://yoursite.com/order-portal`)
- **API:** `{APP_URL}/api/order-portal` (mfano: `https://yoursite.com/api/order-portal`) — routes ziko pia kwenye `routes/api.php`

**Authentication:** Session (cookie). Ili kupata JSON, tuma header:  
`Accept: application/json`  
na kwa requests baada ya login: `Cookie` yenye session (browser/Postman inaweza kuhifadhi cookie baada ya login).

---

## Endpoints – Orodha

| Method | Endpoint | Auth | Maelezo |
|--------|----------|------|---------|
| GET | `/login` | — | Ukurasa wa login (HTML) |
| POST | `/login` | — | Login kwa password |
| POST | `/logout` | Portal | Logout |
| GET | `/orders` | Portal | Orodha ya orders (pending, preparing, served, paid) + tables + menu_items |
| POST | `/orders` | Portal | Tengeneza order mpya |
| PUT | `/orders/{id}` | Portal | Sasisha order (status / details / items) |
| DELETE | `/orders/{id}` | Portal | Futa order |
| POST | `/payments/selcom/initiate` | Portal | Anzisha malipo (USSD) |
| GET | `/payments/selcom/status/{order}` | Portal | Angalia status ya malipo |

(Kama unatumia API base: `POST /api/order-portal/login`, `GET /api/order-portal/orders`, n.k.)

---

## 1. Login

**POST** `/order-portal/login`

Tuma **JSON** na header `Accept: application/json` ili kupata response ya JSON.

### Request (JSON)

```json
{
  "password": "A1B2C3D4"
}
```

| Field | Type | Required | Maelezo |
|-------|------|----------|---------|
| password | string | Ndiyo | Password ya Order Portal (manager ameipa waiter) |

### Response – success (200)

```json
{
  "success": true,
  "message": "Umefanikiwa kuingia.",
  "data": {
    "restaurant_id": 2,
    "restaurant_name": "Samaki Samaki",
    "user_id": 13,
    "user_name": "Juma Waiter"
  }
}
```

### Response – password vibaya (422)

```json
{
  "success": false,
  "message": "Password si sahihi au imekwisha tamaa. Omba mpya kwa manager wako."
}
```

### Response – haana ufikiaji (403)

```json
{
  "success": false,
  "message": "Huna ufikiaji wa Order Portal. Wasiliana na manager wako."
}
```

**Kumbuka:** Baada ya login, server atatuma **session cookie**. Kwa kila request inayofuata (orders, payments), tuma cookie hiyo (browser/Postman kawaida hufanya hivyo automatically).

---

## 2. Logout

**POST** `/order-portal/logout`

Tuma `Accept: application/json` ikiwa unataka JSON.

### Response (200)

```json
{
  "success": true,
  "message": "Umetoka."
}
```

---

## 3. Orodha ya Orders (index)

**GET** `/order-portal/orders`

Inahitaji login (session). Tuma `Accept: application/json`.

### Response (200)

```json
{
  "data": {
    "pending": [
      {
        "id": 101,
        "table_number": "5",
        "customer_phone": "255712345678",
        "customer_name": "John",
        "total_amount": 25000,
        "status": "pending",
        "created_at": "2025-02-26T10:30:00+00:00",
        "items": [
          {
            "id": 201,
            "menu_item_id": 10,
            "name": "Chips Mayai",
            "quantity": 2,
            "price": 5000,
            "total": 10000
          },
          {
            "id": 202,
            "menu_item_id": 12,
            "name": "Soda",
            "quantity": 2,
            "price": 1500,
            "total": 3000
          }
        ]
      }
    ],
    "preparing": [],
    "served": [],
    "paid": []
  },
  "meta": {
    "tables": [
      { "id": 1, "name": "Table 1" },
      { "id": 2, "name": "Table 2" }
    ],
    "menu_items": [
      {
        "id": 10,
        "name": "Chips Mayai",
        "price": 5000,
        "image_url": "https://yoursite.com/storage/serve/menu/xxx.jpg"
      }
    ],
    "restaurant": {
      "id": 2,
      "name": "Samaki Samaki"
    }
  }
}
```

Waiter anaona **orders zake tu** (waiter_id = user aliyelogin).

---

## 4. Tengeneza Order (store)

**POST** `/order-portal/orders`

Body: JSON. Tuma `Accept: application/json`.

### Request (JSON)

```json
{
  "table_number": "5",
  "customer_phone": "255712345678",
  "customer_name": "John",
  "items": [
    { "id": 10, "quantity": 2 },
    { "id": 12, "quantity": 2 }
  ]
}
```

| Field | Type | Required | Maelezo |
|-------|------|----------|---------|
| table_number | string | Ndiyo | Nambari/jina la meza (max 50) |
| customer_phone | string | Hapana | Nambari ya simu |
| customer_name | string | Hapana | Jina la mteja |
| items | array | Ndiyo | Angalau item moja |
| items[].id | int | Ndiyo | `menu_item_id` (lazima ipo kwenye restaurant) |
| items[].quantity | int | Ndiyo | Idadi (≥ 1) |

### Response – success (201)

```json
{
  "message": "Order created successfully.",
  "data": {
    "id": 102,
    "table_number": "5",
    "customer_phone": "255712345678",
    "customer_name": "John",
    "total_amount": 25000,
    "status": "pending",
    "created_at": "2025-02-26T10:35:00+00:00",
    "items": [
      {
        "id": 203,
        "menu_item_id": 10,
        "name": "Chips Mayai",
        "quantity": 2,
        "price": 5000,
        "total": 10000
      }
    ]
  }
}
```

### Response – validation error (422)

```json
{
  "message": "The table number field is required.",
  "errors": {
    "table_number": ["The table number field is required."],
    "items": ["The items field is required."]
  }
}
```

---

## 5. Sasisha Order (update)

**PUT** `/order-portal/orders/{order_id}`

Unaweza kutuma status peke yake, au details (table_number, customer_phone, customer_name), au items (overwrite). Tuma `Accept: application/json`.

### Mfano – badilisha status tu

```json
{
  "status": "preparing"
}
```

Status zinazoruhusiwa: `pending` | `preparing` | `served` | `paid`.

**Confirm paid (kashalipia nje):** Ikiwa mteja kashalipia kwa WhatsApp/cash na unataka kuweka order kama paid bila “Process Payment”, tuma `{"status": "paid"}` kutoka status `served`. Order inaenda Completed.

### Mfano – sasisha details za meza/mteja

```json
{
  "table_number": "6",
  "customer_phone": "255798765432",
  "customer_name": "Jane"
}
```

### Mfano – sasisha items (order items zinaondolewa na kubadilishwa na list mpya)

```json
{
  "items": [
    { "id": 10, "quantity": 3 },
    { "id": 14, "quantity": 1 }
  ]
}
```

`items[].id` = menu_item_id; `quantity` 0 = item haingii (unaweza kuacha kabisa).

### Response – success (200)

```json
{
  "message": "Order updated successfully.",
  "data": {
    "id": 102,
    "table_number": "6",
    "customer_phone": "255798765432",
    "customer_name": "Jane",
    "total_amount": 18000,
    "status": "preparing",
    "created_at": "2025-02-26T10:35:00+00:00",
    "items": [
      {
        "id": 204,
        "menu_item_id": 10,
        "name": "Chips Mayai",
        "quantity": 3,
        "price": 5000,
        "total": 15000
      }
    ]
  }
}
```

---

## 6. Futa Order (destroy)

**DELETE** `/order-portal/orders/{order_id}`

Tuma `Accept: application/json`.

### Response (200)

```json
{
  "message": "Order deleted."
}
```

---

## 7. Anzisha malipo (Selcom USSD)

**POST** `/order-portal/payments/selcom/initiate`

Body: JSON.

### Request (JSON)

```json
{
  "order_id": 102,
  "phone": "255712345678",
  "name": "John"
}
```

| Field | Type | Required | Maelezo |
|-------|------|----------|---------|
| order_id | int | Ndiyo | ID ya order (lazima ya waiter huyu) |
| phone | string | Ndiyo | Nambari ya simu ya mteja (USSD push) |
| name | string | Hapana | Jina la mteja |

### Response – success (200)

```json
{
  "status": "success",
  "message": "USSD Push sent to 255712345678",
  "transaction_id": "ORD-102-1739012345"
}
```

### Response – Selcom haijawekwa (400)

```json
{
  "status": "error",
  "message": "Selcom haijawekwa. Wasiliana na manager."
}
```

### Response – error nyingine (400)

```json
{
  "status": "error",
  "message": "Failed to initiate payment"
}
```

---

## 8. Status ya malipo (Selcom)

**GET** `/order-portal/payments/selcom/status/{order_id}`

Tuma `Accept: application/json` (kawaida inarudi JSON tayari).

### Response – paid (200)

```json
{
  "status": "paid",
  "message": "Payment completed successfully!"
}
```

### Response – pending (200)

```json
{
  "status": "pending",
  "message": "Waiting for payment..."
}
```

### Response – failed (200)

```json
{
  "status": "failed",
  "message": "Payment failed or cancelled"
}
```

### Response – hakuna payment (400)

```json
{
  "status": "error",
  "message": "No active payment found"
}
```

---

## Haijaingia (401)

Ikiwa request kwa endpoint inayohitaji login (orders, payments) inafanywa bila session:

Header: `Accept: application/json`

### Response (401)

```json
{
  "message": "Ingia kwanza kwenye TIPTAP ORDER.",
  "error": "unauthenticated"
}
```

---

## Jumla

- **Login:** POST `/order-portal/login` na body `{ "password": "..." }`, header `Accept: application/json` → response JSON na session cookie.
- **Orders:** GET/POST/PUT/DELETE `/order-portal/orders` (na `/orders/{id}`) zote zinaruhusu JSON ikiwa utatuma `Accept: application/json` na cookie ya session.
- **Payments:** POST initiate, GET status – response zote ni JSON.
- Waiter anaona na kudhibiti **orders zake tu** (waiter_id = user aliyelogin).




Ndiyo. docs/ORDER_PORTAL_API.md ina APIs zote za Order Portal na mifano ya JSON (request na response).
Orodha fupi:
#	Endpoint	Maelezo
1	POST /order-portal/login	Login kwa password – request: {"password":"..."}, response: success/data au error
2	POST /order-portal/logout	Logout – response: {"success":true,"message":"Umetoka."}
3	GET /order-portal/orders	Orodha orders (pending, preparing, served, paid) + meta (tables, menu_items, restaurant) – mfano wa JSON response
4	POST /order-portal/orders	Tengeneza order – body (table_number, items, customer_phone, customer_name) + response 201
5	PUT /order-portal/orders/{id}	Sasisha order – mifano: status, details, items; pia “Confirm paid” (status: "paid")
6	DELETE /order-portal/orders/{id}	Futa order – response message
7	POST /order-portal/payments/selcom/initiate	Anzisha malipo USSD – body (order_id, phone, name) + responses
8	GET /order-portal/payments/selcom/status/{order}	Status ya malipo – paid / pending / failed
—	401	Haijaingia – {"message":"...","error":"
