# Mobile API Integration Guide - Order & Booking System

This document outlines the recent changes and new endpoints for the maintenance order and scheduling system. Use this as context for Antigravity when integrating these features into the mobile app.

---

## 1. Scheduling & Availability

### GET `/api/v1/orders/available-days` (New)
Returns available slots for the next 14 days.
- **Query Params**:
    - `date` (optional): Start date (YYYY-MM-DD). Defaults to `today`.
- **Response Structure**:
  ```json
  {
      "status": true,
      "data": [
          {
              "date": "2026-04-25",
              "day_name": "Saturday",
              "slots": {
                  "am": ["11:00", "11:30"],
                  "pm": ["12:00", "01:30", "04:00"]
              }
          }
      ]
  }
  ```
> **Note**: Only available slots are returned. The `available` boolean key has been removed for simplicity.

### GET `/api/v1/orders/available-slots` (Updated)
Returns slots for a specific day.
- **Required Query Params**: `date=YYYY-MM-DD`
- **Format**: Now returns time strings in **12-hour format** (e.g., `03:00` instead of `15:00`).

---

## 2. Order Management

### POST `/api/v1/orders` (Updated)
Creates a new maintenance order.
- **Body Keys**:
    - `maintenance_id`: ID of the service.
    - `date`: Chosen date (YYYY-MM-DD).
    - `time`: Chosen time slot (e.g., `09:30` or `03:00`).
    - `latitude`, `longitude`, `location_name`, `description`.
- **Validation**:
    - **Past Dates**: Returns an error if the date is in the past.
    - **Overlap Protection**: Prevents booking slots that are already taken or manually blocked by Admin.

### POST `/api/v1/orders/{order_id}/cancel` (New)
Cancels an existing order.
- **Rule**: Can only cancel if status is `new` or `approved`.
- **Response**: Standard success/fail message.

---
 
## 3. Order Listing & Filtering
 
### GET `/api/v1/orders` (New)
Unified endpoint to list and filter user orders.
- **Query Params**:
    - `status` (optional):
        - `current`: Returns orders with status `new` or `approved` (الجاري).
        - `completed`: Returns orders with status `completed` (المنتهية).
        - `cancelled`: Returns orders with status `cancelled` (الملغاة).
    - `page` (optional): For pagination (20 items per page).
 
---

## 3. Important Changes

1. **Time Format**: All time strings in availability endpoints are now **12-hour format** (h:i).
2. **Flexible Input**: The `store` endpoint accepts both 12h and 24h formats for the `time` field, but it is recommended to send exactly what the user picked from the availability list.
3. **Cancellation Flag**: Each order object now includes a `can_cancel` boolean. If `true`, the mobile app should show the "Cancel Order" button. This is based on the order having a `new` or `approved` status.
3. **Smart Notifications**: When an admin approves an order and sets an end time, the push notification to the client will automatically include the time range (e.g., "From 09:00 to 12:00").

---

## 4. Antigravity Prompt Snippet
Copy and paste this into your Antigravity chat if you need help with integration:
> "I am integrating the new maintenance booking system. Please help me implement the scheduling flow using `orders/available-days` for the calendar view and `orders/available-slots` for specific day selection. Note that times are now 12-hour format and we should validate that users don't pick past dates. For cancellation, use `orders/{id}/cancel`."
