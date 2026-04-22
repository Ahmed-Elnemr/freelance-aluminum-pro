# Aluminium Pro - API Changes & Postman Guide

> Documentation for mobile app integration. Use this with Postman collection `Aluminium_Pro_API.postman_collection.json` for API testing and AI-assisted development.

---

## Summary of Changes

The app was refactored from a multi-service structure to **maintenance-only**:

| Before | After |
|--------|-------|
| Category Services | Removed |
| Main Services | Removed |
| Services (products + maintenance) | **Maintenance** only |
| Orders linked to `service_id` | Orders linked to `maintenance_id` |
| Favorites on services | Favorites on maintenances only |
| Service inspection | Maintenance inspection |

---

## Breaking Changes for Mobile App

### 1. Base Path Change

| Old | New |
|-----|-----|
| `/api/v1/services` | `/api/v1/maintenances` |
| `/api/v1/home` returns `main_services` | `/api/v1/home` returns `maintenances` |

### 2. Parameter Renames

| Old | New |
|-----|-----|
| `service_id` | `maintenance_id` |
| `service` in response | `maintenance` or `service_name` (for backward compat in some places) |

### 3. Removed Endpoints

- `GET /api/v1/services` (main services list)
- `GET /api/v1/services/main-services/{id}` (services by main service)
- `GET /api/v1/services/products/list`
- `GET /api/v1/home/main-services/{id}`

### 4. Endpoint Mapping (Old → New)

| Old Endpoint | New Endpoint |
|--------------|--------------|
| `GET /api/v1/services` | `GET /api/v1/maintenances?page=1&per_page=20` |
| `GET /api/v1/services/{id}` | `GET /api/v1/maintenances/{id}` |
| `POST /api/v1/services/search` | `POST /api/v1/maintenances/search` |
| `GET /api/v1/services/maintenance/list` | `GET /api/v1/maintenances/list` |
| `POST /api/v1/services/favourite` (body: `service_id`) | `POST /api/v1/maintenances/favourite` (body: `maintenance_id`) |
| `GET /api/v1/services/my-favorites/get` | `GET /api/v1/maintenances/my-favorites/get` |
| `POST /api/v1/services/rate/{service}` | `POST /api/v1/maintenances/rate/{maintenance}` |
| `POST /api/v1/services/request-inspection` (body: `service_id`) | `POST /api/v1/maintenances/request-inspection` (body: `maintenance_id`) |
| `POST /api/v1/orders` (body: `service_id`) | `POST /api/v1/orders` (body: `maintenance_id`) |

---

## Postman Collection

**File:** `Aluminium_Pro_API.postman_collection.json`  
**Environment:** `Aluminium_Pro_Environment.postman_environment.json`

### Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `base_url` | `http://localhost` | API base URL |
| `access_token` | (empty) | Bearer token (auto-saved after Login) |
| `maintenance_id` | `1` | For single maintenance/rate requests |
| `order_id` | `1` | For order detail requests |

### Import Steps

1. Postman → Import → select `Aluminium_Pro_API.postman_collection.json`
2. Postman → Import → select `Aluminium_Pro_Environment.postman_environment.json`
3. Select **Aluminium Pro - Local** from environment dropdown
4. Set `base_url` (e.g. `https://yourdomain.com`)

---

## API Endpoints Reference

### Auth (Required for protected endpoints)

**Login** – Get Bearer token
```
POST /api/v1/user-auth/login
Content-Type: application/json

{
  "login": "user@example.com",
  "password": "password123",
  "uuid": "device-uuid-string",
  "device_token": "fcm_token",
  "device_type": "android"
}

Response: { "status": true, "data": { "access_token": "1|...", "user": {...} } }
```

---

### Maintenances

#### List Maintenances (Paginated – 20 per page)

```
GET /api/v1/maintenances?page=1&per_page=20
Headers: Accept: application/json, Accept-Language: en
Optional: ?search=keyword
```

**Response:**
```json
{
  "status": true,
  "message": "Loaded Successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "AC Maintenance",
        "content": "Full AC service",
        "final_price": 150,
        "is_favourite": false,
        "base_image": "https://..."
      }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "per_page": 20, "total": 5 }
  }
}
```

#### Get Maintenance by ID

```
GET /api/v1/maintenances/{id}
Headers: Accept: application/json
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "name": "AC Maintenance",
    "content": "Full AC service description",
    "price": 200,
    "final_price": 150,
    "rate": 4.5,
    "ratings_count": 10,
    "my_rating": null,
    "is_favourite": false,
    "base_image": "https://...",
    "images": ["https://...", "https://..."]
  }
}
```

#### Search Maintenances

```
POST /api/v1/maintenances/search
Content-Type: application/json

{
  "search": "maintenance",
  "page": 1,
  "per_page": 20
}
```

#### Simple List (IDs and names)

```
GET /api/v1/maintenances/list
```

**Response:**
```json
{
  "status": true,
  "data": [
    { "id": 1, "name": "AC Maintenance" },
    { "id": 2, "name": "Plumbing" }
  ]
}
```

#### Toggle Favorite (Auth required)

```
POST /api/v1/maintenances/favourite
Authorization: Bearer {token}
Content-Type: application/json

{ "maintenance_id": 1 }
```

#### My Favorites (Auth required)

```
GET /api/v1/maintenances/my-favorites/get
Authorization: Bearer {token}
```

#### Rate Maintenance (Auth required)

```
POST /api/v1/maintenances/rate/{maintenance_id}
Authorization: Bearer {token}
Content-Type: application/json

{ "rating": 5 }
```

#### Request Inspection (Auth required)

```
POST /api/v1/maintenances/request-inspection
Authorization: Bearer {token}
Content-Type: application/json

{ "maintenance_id": 1 }
```

---

### Orders (All require Auth)

**Header:** `Authorization: Bearer {access_token}`

#### Create Order

```
POST /api/v1/orders
Content-Type: multipart/form-data  (when including files)
or
Content-Type: application/json    (when no files)
```

**Body (JSON or form-data):**
```json
{
  "maintenance_id": 1,
  "latitude": 24.7136,
  "longitude": 46.6753,
  "location_name": "Riyadh, King Fahd Road",
  "description": "AC not cooling properly",
  "internal_note": "Morning preferred",
  "date": "2026-03-20",
  "time": "10:00",
  "paymentmethod": 1
}
```

**Optional files:** `images[]` (jpg, png, webp, etc.), `sounds[]` (mp3, wav, etc.)

**Response:**
```json
{
  "status": true,
  "data": {
    "payment_url": "https://yourdomain.com/payment-page/123"
  }
}
```

#### Current Orders

```
GET /api/v1/orders/current?page=1
```

#### Expired Orders

```
GET /api/v1/orders/expired?page=1
```

#### Order Detail

```
GET /api/v1/orders/{order_id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "user_name": "John",
    "service_name": "AC Maintenance",
    "location": "Riyadh",
    "location_name": "King Fahd Road",
    "latitude": 24.71,
    "longitude": 46.67,
    "maintenance_id": 1,
    "price": 200,
    "final_price": 150,
    "description": "...",
    "internal_note": "...",
    "status": "current",
    "status_label": "Current",
    "date": "2026-03-20",
    "time": "10:00",
    "formatted_time": "10:00 AM",
    "media": [{"url": "...", "type": "image/jpeg"}],
    "sounds": [{"url": "...", "type": "audio/mpeg"}]
  }
}
```

---

## Home API

```
GET /api/v1/home
```

**Response (changed):**
```json
{
  "status": true,
  "data": {
    "sliders": [...],
    "maintenances": {
      "data": [...],
      "links": {...},
      "meta": {...}
    }
  }
}
```

---

## Validation Rules (Create Order)

| Field | Rule |
|-------|------|
| maintenance_id | required, exists in maintenances, active |
| latitude | required, numeric, between -90 and 90 |
| longitude | required, numeric, between -180 and 180 |
| location_name | required, string, max 255 |
| description | optional, string, max 1000 |
| internal_note | optional, string, max 1000 |
| date | required, format Y-m-d |
| time | required, format H:i |
| paymentmethod | required, 1 (Moyasar) |
| images | optional, array of images |
| sounds | optional, array of audio files |

---

## Error Response Format

```json
{
  "status": false,
  "message": "Error description"
}
```

Validation errors (422):
```json
{
  "message": "The maintenance id field is required.",
  "errors": {
    "maintenance_id": ["The maintenance id field is required."]
  }
}
```

---

## Mobile App Migration Checklist

- [ ] Replace `service_id` with `maintenance_id` in all requests
- [ ] Change base path from `/services` to `/maintenances` for maintenance-related calls
- [ ] Update Home screen to use `maintenances` instead of `main_services`
- [ ] Update favorites: use `maintenance_id` in toggle and expect maintenances in my-favorites
- [ ] Update order creation: send `maintenance_id`
- [ ] Update order list/detail: read `maintenance_id`, `service_name` (maintenance name)
- [ ] Update inspection request: use `maintenance_id`

---

## Postman Collection Structure

```
Aluminium Pro - Maintenance & Orders API
├── Auth
│   └── Login
├── Maintenances
│   ├── List Maintenances (Paginated - 20 per page)
│   ├── Get Maintenance by ID
│   ├── Search Maintenances
│   ├── Maintenance List (IDs & Names)
│   ├── Toggle Favorite (Auth Required)
│   ├── My Favorites (Auth Required)
│   ├── Rate Maintenance (Auth Required)
│   └── Request Inspection (Auth Required)
└── Orders
    ├── Create Order (Auth Required)
    ├── Create Order - JSON Example (No Files)
    ├── Current Orders (Auth Required)
    ├── Expired Orders (Auth Required)
    └── Get Order by ID (Auth Required)
```

---

**Last Updated:** 2026-03-14
