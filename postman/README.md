# Aluminium Pro - Postman Collection

## Import

1. **Import Collection:** Postman → Import → `Aluminium_Pro_API.postman_collection.json`
2. **Import Environment:** Postman → Import → `Aluminium_Pro_Environment.postman_environment.json`
3. Select **Aluminium Pro - Local** environment from the dropdown

## Setup

Update `base_url` in the environment (e.g. `http://localhost:8000` or your API URL).

## Quick Start

1. **Login** – Run "Auth > Login" with valid credentials. Token is saved automatically.
2. **List Maintenances** – Run "Maintenances > List Maintenances" (paginated, 20 per page).
3. **Get Maintenance** – Run "Maintenances > Get Maintenance by ID" (use `maintenance_id` variable).
4. **Create Order** – Run "Orders > Create Order" (requires auth).

## Endpoints Overview

### Maintenances
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/v1/maintenances?page=1&per_page=20` | No | List maintenances (paginated, 20 default) |
| GET | `/api/v1/maintenances/{id}` | No | Get maintenance by ID |
| POST | `/api/v1/maintenances/search` | No | Search maintenances |
| GET | `/api/v1/maintenances/list` | No | Simple id/name list |
| POST | `/api/v1/maintenances/favourite` | Yes | Toggle favorite |
| GET | `/api/v1/maintenances/my-favorites/get` | Yes | My favorites |
| POST | `/api/v1/maintenances/rate/{id}` | Yes | Rate maintenance |
| POST | `/api/v1/maintenances/request-inspection` | Yes | Request inspection |

### Orders
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/v1/orders` | Yes | Create order |
| GET | `/api/v1/orders/current` | Yes | Current orders (paginated) |
| GET | `/api/v1/orders/expired` | Yes | Expired orders (paginated) |
| GET | `/api/v1/orders/{id}` | Yes | Order details |

## Create Order Example

```json
{
  "maintenance_id": 1,
  "latitude": 24.7136,
  "longitude": 46.6753,
  "location_name": "Riyadh, Saudi Arabia",
  "description": "AC not cooling",
  "internal_note": "Morning preferred",
  "date": "2026-03-20",
  "time": "10:00",
  "paymentmethod": 1
}
```

Use **form-data** when attaching `images[]` or `sounds[]` files.

## Response Format

All responses follow:
```json
{
  "status": true,
  "message": "Loaded Successfully",
  "data": { ... }
}
```

Paginated data includes `data`, `links`, and `meta` in the `data` object.
