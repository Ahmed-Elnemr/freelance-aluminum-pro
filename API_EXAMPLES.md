# أمثلة API - نظام المصادقة الجديد

## 1. التسجيل (Register)

### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "mobile": "0512345678",
    "password": "Password@123",
    "password_confirmation": "Password@123",
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "device_token": "fcm_token_here",
    "device_type": "android"
  }'
```

### Postman
```
Method: POST
URL: http://your-domain.com/api/v1/user-auth/register
Headers:
  Content-Type: application/json
  Accept: application/json
  Accept-Language: ar

Body (raw JSON):
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "mobile": "0512345678",
  "password": "Password@123",
  "password_confirmation": "Password@123",
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "device_token": "fcm_token_here",
  "device_type": "android"
}
```

### Response (Success - 201)
```json
{
  "status": true,
  "message": "تم إنشاء الحساب بنجاح",
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "mobile": "0512345678",
      "type": null,
      "is_active": 1,
      "status": 1
    },
    "access_token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

### Response (Error - 422)
```json
{
  "status": false,
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "البريد الإلكتروني مُستخدم من قبل."
    ]
  }
}
```

---

## 2. تسجيل الدخول بالبريد الإلكتروني (Login with Email)

### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "login": "ahmed@example.com",
    "password": "Password@123",
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "device_token": "fcm_token_here",
    "device_type": "android"
  }'
```

### Postman
```
Method: POST
URL: http://your-domain.com/api/v1/user-auth/login
Headers:
  Content-Type: application/json
  Accept: application/json
  Accept-Language: ar

Body (raw JSON):
{
  "login": "ahmed@example.com",
  "password": "Password@123",
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "device_token": "fcm_token_here",
  "device_type": "android"
}
```

### Response (Success - 200)
```json
{
  "status": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "mobile": "0512345678",
      "type": null,
      "is_active": 1,
      "status": 1
    },
    "access_token": "2|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

### Response (Error - 401)
```json
{
  "status": false,
  "message": "البريد الإلكتروني/رقم الهاتف أو كلمة المرور غير صحيحة"
}
```

---

## 3. تسجيل الدخول برقم الهاتف (Login with Mobile)

### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -d '{
    "login": "0512345678",
    "password": "Password@123",
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "device_token": "fcm_token_here",
    "device_type": "android"
  }'
```

### Postman
```
Method: POST
URL: http://your-domain.com/api/v1/user-auth/login
Headers:
  Content-Type: application/json
  Accept: application/json
  Accept-Language: ar

Body (raw JSON):
{
  "login": "0512345678",
  "password": "Password@123",
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "device_token": "fcm_token_here",
  "device_type": "android"
}
```

---

## 4. عرض الملف الشخصي (Get Profile)

### cURL
```bash
curl -X GET http://your-domain.com/api/v1/user-auth/profile \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Postman
```
Method: GET
URL: http://your-domain.com/api/v1/user-auth/profile
Headers:
  Accept: application/json
  Accept-Language: ar
  Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Response (Success - 200)
```json
{
  "status": true,
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "mobile": "0512345678",
      "type": null,
      "is_active": 1,
      "status": 1
    }
  }
}
```

---

## 5. تحديث الملف الشخصي (Update Profile)

### تحديث الاسم والبريد الإلكتروني

#### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/edite-profile \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "name": "أحمد علي",
    "email": "ahmed.ali@example.com"
  }'
```

#### Postman
```
Method: POST
URL: http://your-domain.com/api/v1/user-auth/edite-profile
Headers:
  Content-Type: application/json
  Accept: application/json
  Accept-Language: ar
  Authorization: Bearer YOUR_ACCESS_TOKEN

Body (raw JSON):
{
  "name": "أحمد علي",
  "email": "ahmed.ali@example.com"
}
```

### تحديث رقم الهاتف

#### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/edite-profile \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "mobile": "0598765432"
  }'
```

### تغيير كلمة المرور

#### cURL
```bash
curl -X POST http://your-domain.com/api/v1/user-auth/edite-profile \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "current_password": "Password@123",
    "password": "NewPassword@456",
    "password_confirmation": "NewPassword@456"
  }'
```

#### Postman
```
Method: POST
URL: http://your-domain.com/api/v1/user-auth/edite-profile
Headers:
  Content-Type: application/json
  Accept: application/json
  Accept-Language: ar
  Authorization: Bearer YOUR_ACCESS_TOKEN

Body (raw JSON):
{
  "current_password": "Password@123",
  "password": "NewPassword@456",
  "password_confirmation": "NewPassword@456"
}
```

### Response (Success - 200)
```json
{
  "status": true,
  "message": "تم تحديث الملف الشخصي بنجاح",
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد علي",
      "email": "ahmed.ali@example.com",
      "mobile": "0512345678",
      "type": null,
      "is_active": 1,
      "status": 1
    }
  }
}
```

### Response (Error - كلمة المرور الحالية خاطئة)
```json
{
  "status": false,
  "message": "كلمة المرور الحالية غير صحيحة"
}
```

---

## 6. تسجيل الخروج (Logout)

### cURL
```bash
curl -X GET http://your-domain.com/api/v1/user-auth/logout?uuid=550e8400-e29b-41d4-a716-446655440000 \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Postman
```
Method: GET
URL: http://your-domain.com/api/v1/user-auth/logout?uuid=550e8400-e29b-41d4-a716-446655440000
Headers:
  Accept: application/json
  Accept-Language: ar
  Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Response (Success - 200)
```json
{
  "status": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

---

## 7. حذف الحساب (Delete Account)

### cURL
```bash
curl -X DELETE http://your-domain.com/api/v1/user-auth/delete-account \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Postman
```
Method: DELETE
URL: http://your-domain.com/api/v1/user-auth/delete-account
Headers:
  Accept: application/json
  Accept-Language: ar
  Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Response (Success - 200)
```json
{
  "status": true,
  "message": "تم حذف حسابك بنجاح"
}
```

---

## ملاحظات مهمة

### Headers الإلزامية
- `Accept: application/json` - لضمان الحصول على استجابة JSON
- `Content-Type: application/json` - للطلبات التي تحتوي على body
- `Accept-Language: ar` أو `en` - لتحديد لغة الرسائل

### Authentication
- استخدم `Authorization: Bearer {token}` للـ endpoints المحمية
- احصل على الـ token من استجابة التسجيل أو تسجيل الدخول

### قواعد كلمة المرور
- 8 أحرف على الأقل
- يجب أن تحتوي على:
  - أحرف كبيرة (A-Z)
  - أحرف صغيرة (a-z)
  - أرقام (0-9)
  - رموز خاصة (!@#$%^&*)

### رقم الهاتف
- يجب أن يبدأ بـ 05
- يتبعه 8 أرقام
- مثال: 0512345678

### UUID الجهاز
- يمكن استخدام UUID عشوائي للاختبار
- في التطبيق الفعلي، استخدم UUID فريد لكل جهاز
