# 📦 ملفات Postman - نظام المصادقة الجديد

## ✅ تم إنشاء الملفات التالية

### 1. Postman Collection
📄 **`Rainz_Auth_API.postman_collection.json`**

يحتوي على:
- ✅ **10 Requests نشطة** (جديدة ومعدلة)
- ❌ **3 Requests محذوفة** (للمرجعية فقط)
- 🔄 **Auto-save للـ token** بعد Login/Register

#### الـ Requests المتوفرة:

**الجديدة ✅**
1. Register (NEW)
7. Change Password (NEW)

**المعدلة 🔄**
2. Login with Email (UPDATED)
3. Login with Mobile (UPDATED)
5. Update Profile - Name & Email (UPDATED)
6. Update Profile - Mobile (UPDATED)

**بدون تغيير ✓**
4. Get Profile
8. Store Name
9. Logout
10. Delete Account

**المحذوفة ❌ (في مجلد منفصل)**
- Activate OTP
- Resend OTP Code
- Confirm Mobile Change

---

### 2. Postman Environment
📄 **`Rainz_Auth_Development.postman_environment.json`**

المتغيرات المتوفرة:
- `base_url` - عنوان الـ API
- `access_token` - يحفظ تلقائياً
- `user_id` - يحفظ تلقائياً
- `device_uuid` - معرف الجهاز
- `device_token` - FCM token
- `test_email` - بريد للاختبار
- `test_mobile` - هاتف للاختبار
- `test_password` - كلمة مرور للاختبار

---

### 3. دليل الاستخدام
📄 **`POSTMAN_GUIDE.md`**

يحتوي على:
- كيفية الاستيراد
- إعداد Environment
- دورة الاختبار الكاملة
- أمثلة الاستجابات
- نصائح الاستخدام

---

## 🚀 البدء السريع

### الخطوة 1: استيراد الملفات
```
1. افتح Postman
2. Import → Rainz_Auth_API.postman_collection.json
3. Import → Rainz_Auth_Development.postman_environment.json
4. اختر Environment: "Rainz Auth - Development"
```

### الخطوة 2: تحديث base_url
```
1. اضغط على Environment في الزاوية العلوية
2. عدّل base_url إلى عنوان الـ API الخاص بك
   مثال: http://localhost:8000
```

### الخطوة 3: اختبار التسجيل
```
1. افتح Request: "1. Register (NEW)"
2. اضغط Send
3. سيتم حفظ الـ token تلقائياً
```

### الخطوة 4: اختبار باقي الـ Requests
```
1. Get Profile
2. Update Profile
3. Change Password
4. Logout
```

---

## 🔄 دورات الاختبار المقترحة

### السيناريو 1: مستخدم جديد كامل
```
1. Register (NEW) ✅
2. Get Profile
3. Update Profile - Name & Email
4. Change Password
5. Logout
6. Login with Email (بكلمة المرور الجديدة)
```

### السيناريو 2: تسجيل دخول موجود
```
1. Login with Email ✅
2. Get Profile
3. Update Profile - Mobile
4. Logout
```

### السيناريو 3: تغيير كلمة المرور
```
1. Login with Mobile ✅
2. Change Password
3. Logout
4. Login with Email (بكلمة المرور الجديدة)
```

---

## 📋 التغييرات الرئيسية في الـ Requests

### Login (تم التعديل 🔄)

**قبل (OTP):**
```json
{
  "mobile": "0512345678",
  "uuid": "...",
  "device_token": "...",
  "device_type": "android"
}
```

**بعد (Password):**
```json
{
  "login": "ahmed@example.com",  // أو "0512345678"
  "password": "Password@123",
  "uuid": "...",
  "device_token": "...",
  "device_type": "android"
}
```

### Register (جديد ✅)

```json
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "mobile": "0512345678",
  "password": "Password@123",
  "password_confirmation": "Password@123",
  "uuid": "...",
  "device_token": "...",
  "device_type": "android"
}
```

### Update Profile (تم التعديل 🔄)

**الآن يدعم:**
```json
{
  "name": "أحمد علي",           // اختياري
  "email": "new@example.com",    // اختياري - جديد ✅
  "mobile": "0598765432",        // اختياري - بدون OTP ✅
  "current_password": "...",     // مطلوب مع password
  "password": "NewPass@123",     // اختياري - جديد ✅
  "password_confirmation": "..." // مطلوب مع password
}
```

---

## 🎯 الميزات الخاصة بالـ Collection

### 1. Auto-Save للـ Token
بعد Login أو Register، يتم حفظ:
- `access_token` تلقائياً
- `user_id` تلقائياً

### 2. Pre-filled Variables
جميع الـ requests تستخدم المتغيرات:
- `{{base_url}}`
- `{{access_token}}`
- `{{device_uuid}}`
- `{{device_token}}`

### 3. وصف مفصل
كل request يحتوي على:
- ✅ الوصف بالعربية
- ✅ الحقول المطلوبة
- ✅ التغييرات من النظام القديم
- ✅ أمثلة الاستجابات

### 4. مجلد الـ Requests المحذوفة
للمرجعية فقط - لا تستخدمها! ❌

---

## 📊 جدول مقارنة الـ Endpoints

| الـ Endpoint | قبل | بعد | الحالة |
|-------------|-----|-----|--------|
| `/register` | ❌ لا يوجد | ✅ موجود | جديد |
| `/login` | OTP | Email/Mobile + Password | معدل |
| `/activate` | ✅ موجود | ❌ محذوف | محذوف |
| `/resend-code` | ✅ موجود | ❌ محذوف | محذوف |
| `/confirm-new-mobile` | ✅ موجود | ❌ محذوف | محذوف |
| `/edite-profile` | Mobile فقط | Email + Mobile + Password | معدل |
| `/profile` | ✓ | ✓ | بدون تغيير |
| `/logout` | ✓ | ✓ | بدون تغيير |
| `/delete-account` | ✓ | ✓ | بدون تغيير |

---

## 🔐 قواعد Validation

### كلمة المرور
- ✅ 8 أحرف على الأقل
- ✅ أحرف كبيرة وصغيرة
- ✅ أرقام
- ✅ رموز خاصة

**مثال:** `Password@123`

### رقم الهاتف
- ✅ يبدأ بـ `05`
- ✅ 10 أرقام إجمالاً

**مثال:** `0512345678`

### البريد الإلكتروني
- ✅ صيغة email صحيحة
- ✅ فريد (غير مكرر)

**مثال:** `ahmed@example.com`

---

## 📞 الدعم والمساعدة

### للمزيد من المعلومات:
1. **`POSTMAN_GUIDE.md`** - دليل الاستخدام الكامل
2. **`AUTH_SYSTEM_UPDATES.md`** - توثيق النظام
3. **`API_EXAMPLES.md`** - أمثلة cURL

### إذا واجهت مشاكل:
1. ✅ تأكد من تشغيل الـ server
2. ✅ تأكد من اختيار الـ Environment
3. ✅ تأكد من صحة `base_url`
4. ✅ راجع الـ Console في Postman

---

## ✅ Checklist الاستخدام

- [ ] استيراد Collection
- [ ] استيراد Environment
- [ ] اختيار Environment النشط
- [ ] تحديث `base_url`
- [ ] اختبار Register
- [ ] اختبار Login with Email
- [ ] اختبار Login with Mobile
- [ ] اختبار Update Profile
- [ ] اختبار Change Password
- [ ] اختبار Logout

---

**تاريخ الإنشاء:** 2026-02-02
**الإصدار:** 2.0
**الحالة:** ✅ جاهز للاستخدام

🎉 **جميع الملفات جاهزة! ابدأ الاختبار الآن!**
