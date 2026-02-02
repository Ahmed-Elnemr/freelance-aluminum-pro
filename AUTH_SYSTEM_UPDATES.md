# تحديثات نظام المصادقة (Authentication System Updates)

## التغييرات الرئيسية (Main Changes)

تم تحويل نظام المصادقة من نظام OTP إلى نظام تقليدي يعتمد على البريد الإلكتروني/الهاتف وكلمة المرور.

### 1. الملفات الجديدة (New Files)

#### Request Classes
- `app/Http/Requests/auth/RegisterRequest.php` - طلب التسجيل الجديد

#### Migrations
- `database/migrations/2026_02_02_164900_make_password_required_in_users_table.php` - جعل حقول password و email إلزامية

### 2. الملفات المعدلة (Modified Files)

#### Controllers
- `app/Http/Controllers/Api/AuthController.php`
  - تم تعديل دالة `login()` لدعم تسجيل الدخول بالبريد الإلكتروني أو الهاتف مع كلمة المرور
  - تم إضافة دالة `register()` لتسجيل حساب جديد
  - تم تعديل دالة `editeProfile()` لدعم تغيير البريد الإلكتروني وكلمة المرور

#### Request Classes
- `app/Http/Requests/auth/UserLoginRequest.php` - تم تعديله لقبول login (email/mobile) و password
- `app/Http/Requests/auth/UserEditeProfile.php` - تم تعديله لدعم تحديث البريد الإلكتروني وكلمة المرور

#### Routes
- `routes/v1/auth.php`
  - تم إضافة route للتسجيل: `POST /user-auth/register`
  - تم إزالة routes الخاصة بـ OTP: `activate`, `resend-code`, `confirm-new-mobile`

#### Language Files
- `lang/ar/auth.php` - إضافة رسائل المصادقة بالعربية
- `lang/en/auth.php` - إضافة رسائل المصادقة بالإنجليزية
- `lang/ar/validation.php` - إضافة attributes التحقق بالعربية
- `lang/en/validation.php` - إضافة attributes التحقق بالإنجليزية

## API Endpoints

### 1. التسجيل (Register)
```
POST /api/v1/user-auth/register
```

**Request Body:**
```json
{
    "name": "Ahmed Mohamed",
    "email": "ahmed@example.com",
    "mobile": "0512345678",
    "password": "Password@123",
    "password_confirmation": "Password@123",
    "uuid": "device-uuid-here",
    "device_token": "fcm-token-here",
    "device_type": "android"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "تم إنشاء الحساب بنجاح",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmed Mohamed",
            "email": "ahmed@example.com",
            "mobile": "0512345678"
        },
        "access_token": "token-here"
    }
}
```

### 2. تسجيل الدخول (Login)
```
POST /api/v1/user-auth/login
```

**Request Body:**
```json
{
    "login": "ahmed@example.com",  // أو رقم الهاتف: "0512345678"
    "password": "Password@123",
    "uuid": "device-uuid-here",
    "device_token": "fcm-token-here",
    "device_type": "android"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmed Mohamed",
            "email": "ahmed@example.com",
            "mobile": "0512345678"
        },
        "access_token": "token-here"
    }
}
```

### 3. تحديث الملف الشخصي (Update Profile)
```
POST /api/v1/user-auth/edite-profile
Authorization: Bearer {token}
```

**Request Body (تحديث الاسم والبريد الإلكتروني):**
```json
{
    "name": "Ahmed Ali",
    "email": "newemail@example.com",
    "mobile": "0598765432"
}
```

**Request Body (تغيير كلمة المرور):**
```json
{
    "current_password": "OldPassword@123",
    "password": "NewPassword@123",
    "password_confirmation": "NewPassword@123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "تم تحديث الملف الشخصي بنجاح",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmed Ali",
            "email": "newemail@example.com",
            "mobile": "0598765432"
        }
    }
}
```

## قواعد التحقق (Validation Rules)

### التسجيل (Register)
- **name**: مطلوب، نص، من 2 إلى 255 حرف
- **email**: مطلوب، بريد إلكتروني صحيح، فريد
- **mobile**: مطلوب، يبدأ بـ 05 ويتبعه 8 أرقام، فريد
- **password**: مطلوب، 8 أحرف على الأقل، يحتوي على:
  - أحرف كبيرة وصغيرة
  - أرقام
  - رموز خاصة
- **password_confirmation**: مطلوب، يطابق password

### تسجيل الدخول (Login)
- **login**: مطلوب، بريد إلكتروني أو رقم هاتف
- **password**: مطلوب، 8 أحرف على الأقل

### تحديث الملف الشخصي (Update Profile)
- **name**: اختياري، نص، من 2 إلى 255 حرف
- **email**: اختياري، بريد إلكتروني صحيح، فريد
- **mobile**: اختياري، يبدأ بـ 05 ويتبعه 8 أرقام، فريد
- **current_password**: مطلوب عند تغيير كلمة المرور
- **password**: اختياري، 8 أحرف على الأقل
- **password_confirmation**: مطلوب عند تغيير كلمة المرور

## رسائل الخطأ (Error Messages)

### أمثلة على رسائل الخطأ بالعربية:
- `"البريد الإلكتروني/رقم الهاتف أو كلمة المرور غير صحيحة"` - بيانات تسجيل الدخول خاطئة
- `"حسابك محظور"` - الحساب غير نشط
- `"كلمة المرور الحالية مطلوبة"` - عند محاولة تغيير كلمة المرور بدون إدخال الحالية
- `"كلمة المرور الحالية غير صحيحة"` - كلمة المرور الحالية خاطئة
- `"رقم الهاتف يجب أن يبدأ بـ 05 ويتبعه 8 أرقام"` - صيغة رقم الهاتف خاطئة

## خطوات التطبيق (Implementation Steps)

### 1. تشغيل Migration
```bash
php artisan migrate
```

### 2. تحديث البيانات الموجودة (إذا لزم الأمر)
إذا كان لديك مستخدمين حاليين بدون كلمات مرور، يجب تحديث بياناتهم أو إنشاء كلمات مرور افتراضية لهم.

```php
// مثال على تحديث المستخدمين الحاليين
User::whereNull('password')->update([
    'password' => Hash::make('DefaultPassword@123')
]);
```

### 3. إزالة الكود القديم (اختياري)
يمكنك إزالة الملفات التالية إذا لم تعد بحاجة إليها:
- `app/Http/Requests/auth/CheckMobileOtpRequest.php`
- `app/Http/Requests/auth/SendMobileOtpRequest.php`
- `app/Http/Requests/auth/ConfirmNewMobileRequest.php`
- دالة `activate()` في `app/Service/ConfirmationController.php`
- دالة `resendCode()` في `app/Service/ConfirmationController.php`
- دالة `confirmMobileChange()` في `app/Http/Controllers/Api/AuthController.php`

## ملاحظات مهمة (Important Notes)

1. **كلمة المرور**: يتم تشفير كلمة المرور تلقائياً باستخدام `Hash::make()`
2. **تسجيل الدخول المرن**: يمكن للمستخدم تسجيل الدخول باستخدام البريد الإلكتروني أو رقم الهاتف
3. **التحقق من كلمة المرور الحالية**: عند تغيير كلمة المرور، يجب إدخال كلمة المرور الحالية للتأكد من هوية المستخدم
4. **الأمان**: تم تطبيق قواعد قوية لكلمة المرور (أحرف كبيرة وصغيرة، أرقام، رموز)
5. **الرسائل متعددة اللغات**: جميع الرسائل متوفرة بالعربية والإنجليزية

## الاختبار (Testing)

### اختبار التسجيل
1. أرسل طلب POST إلى `/api/v1/user-auth/register` مع البيانات المطلوبة
2. تأكد من استلام token في الاستجابة
3. تحقق من إنشاء المستخدم في قاعدة البيانات

### اختبار تسجيل الدخول
1. أرسل طلب POST إلى `/api/v1/user-auth/login` مع البريد الإلكتروني/الهاتف وكلمة المرور
2. تأكد من استلام token في الاستجابة
3. جرب تسجيل الدخول بالبريد الإلكتروني مرة وبرقم الهاتف مرة أخرى

### اختبار تحديث الملف الشخصي
1. أرسل طلب POST إلى `/api/v1/user-auth/edite-profile` مع token صحيح
2. جرب تحديث الاسم والبريد الإلكتروني
3. جرب تغيير كلمة المرور مع التحقق من كلمة المرور الحالية
