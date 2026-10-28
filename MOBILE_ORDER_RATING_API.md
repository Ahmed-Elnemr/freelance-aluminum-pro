# Mobile API — تقييم الطلب (جاهز على الباك)

الملف ده للتطبيق. الباك نفّذ التقييم المرتبط بالطلب. انسخه في مشروع الموبايل وافتحه مع Cursor عشان يربط الشاشة بالـ API من غير ما يخترع عقد جديد.

التقييم القديم `POST /api/v1/maintenances/rate/{id}` لسه موجود ومش مطلوب استخدامه. التقييم الجديد مربوط بالطلب مرة واحدة.

---

## Headers

نفس باقي الـ API:

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/x-www-form-urlencoded
lang: ar | en
```

`lang` هو اللي يحدد لغة `message`. لو مش موجود، الباك يستخدم `Accept-Language`.

---

## 1. حقول جديدة على الطلب

موجودة في:

- `GET /api/v1/orders?status=current|completed|cancelled&page=`
- `GET /api/v1/orders/current`
- `GET /api/v1/orders/expired`
- `GET /api/v1/orders/{id}`

باقي حقول الطلب زي ما هي. الاتنين الجداد:

| الحقل | النوع | المعنى |
|---|---|---|
| `can_rate` | bool | `true` فقط لو الطلب `completed` ومش متقيّم وتبع العميل الحالي |
| `rating` | object أو `null` | تقييم العميل، أو `null` لو لسه |

مفيش مدة صلاحية. أي طلب مكتمل يتقيّم مرة واحدة، ومفيش تعديل ولا حذف من التطبيق.

زرار التقييم يظهر لما `can_rate == true` فقط. لو الطلب اتقيّم، `can_rate` يبقى `false` و `rating` يبقى موجود.

### طلب مكتمل ولسه متقيّمش

```json
{
  "id": 123,
  "status": "completed",
  "status_label": "منتهي",
  "can_rate": true,
  "rating": null
}
```

### طلب اتقيّم

```json
{
  "id": 123,
  "status": "completed",
  "can_rate": false,
  "rating": {
    "id": 31,
    "stars": 4,
    "comment": "الفني محترم وشغله نضيف",
    "tags": ["on_time", "quality"],
    "created_at": "2026-10-28T15:30:00+00:00"
  }
}
```

شكل `rating` ثابت:

| الحقل | النوع | ملاحظات |
|---|---|---|
| `id` | int | |
| `stars` | int | 1–5 |
| `comment` | string أو null | |
| `tags` | array of strings | `[]` لو مفيش |
| `created_at` | string | ISO 8601 |

طلب جاري أو ملغي: `can_rate: false` و `rating: null`.

---

## 2. إرسال التقييم

```
POST /api/v1/orders/{id}/rate
```

Body (`application/x-www-form-urlencoded`):

| الحقل | إجباري | القاعدة |
|---|---|---|
| `stars` | نعم | integer من 1 إلى 5 |
| `comment` | لا | string، أقصى 500 حرف. متبعتوش لو فاضي |
| `tags[]` | لا | حتى 10 مفاتيح من الجدول تحت. متبعتوش لو فاضي |

مثال:

```
stars=4&comment=الفني محترم&tags[0]=on_time&tags[1]=quality
```

### 201 Created

```json
{
  "status": true,
  "message": "شكراً لتقييمك!",
  "data": {
    "id": 31,
    "stars": 4,
    "comment": "الفني محترم وشغله نضيف",
    "tags": ["on_time", "quality"],
    "created_at": "2026-10-28T15:30:00+00:00"
  }
}
```

`data` هو كائن التقييم نفسه. اعرضه فورًا في تفاصيل الطلب والقائمة، وخلّي `can_rate = false`.

نجوم بس من غير تعليق وتاجات:

```json
{
  "status": true,
  "message": "شكراً لتقييمك!",
  "data": {
    "id": 32,
    "stars": 5,
    "comment": null,
    "tags": [],
    "created_at": "2026-10-28T15:31:00+00:00"
  }
}
```

### 409 اتقيّم قبل كده

```json
{
  "status": false,
  "code": "order_already_rated",
  "message": "لقد قمت بتقييم هذا الطلب مسبقاً",
  "data": {
    "id": 31,
    "stars": 4,
    "comment": "الفني محترم وشغله نضيف",
    "tags": ["on_time", "quality"],
    "created_at": "2026-10-28T15:30:00+00:00"
  }
}
```

اقفل شاشة التقييم واعرض `data`. ده تقييم العميل حتى لو جه من جهاز تاني.

### 403 أو 422 مش مسموح

```json
{
  "status": false,
  "code": "order_not_ratable",
  "message": "لا يمكن تقييم هذا الطلب"
}
```

- **403**: الطلب مش بتاع العميل.
- **422** مع نفس `code`: الطلب مش `completed`.

اعرض `message` واعمل refresh للطلب عشان `can_rate` يتحدث.

### 422 validation

```json
{
  "status": false,
  "message": "التقييم يجب أن يكون بين 1 و 5",
  "errors": {
    "stars": ["التقييم يجب أن يكون بين 1 و 5"]
  }
}
```

اعرض `message` وسيب الشاشة مفتوحة. مفيش `code` في رد الـ validation.

### الرسائل

| الحالة | ar | en |
|---|---|---|
| نجاح | شكراً لتقييمك! | Thanks for your rating! |
| اتقيّم قبل كده | لقد قمت بتقييم هذا الطلب مسبقاً | You have already rated this order |
| مش مسموح | لا يمكن تقييم هذا الطلب | This order can't be rated |
| نجوم غلط | التقييم يجب أن يكون بين 1 و 5 | The rating must be between 1 and 5 |
| تعليق أطول من 500 | التعليق يجب ألا يتجاوز 500 حرف | The comment may not be greater than 500 characters |
| تاج غلط | أحد وسوم التقييم غير صالح | One of the rating tags is invalid |

`code` قيمه بالظبط: `order_already_rated` أو `order_not_ratable`.

---

## 3. التاجات

الباك بيخزن المفتاح بس. المفاتيح متتغيّرش.

| المفتاح | متى يظهر في التطبيق | العربي | English |
|---|---|---|---|
| `on_time` | 4 أو 5 نجوم | الالتزام بالموعد | On time |
| `quality` | 4 أو 5 نجوم | جودة التنفيذ | Quality work |
| `professional` | 4 أو 5 نجوم | احترافية الفريق | Professional team |
| `clean` | 4 أو 5 نجوم | نظافة المكان | Left it clean |
| `fair_price` | 4 أو 5 نجوم | سعر مناسب | Fair price |
| `late` | 3 أو أقل | تأخير في الموعد | Arrived late |
| `poor_quality` | 3 أو أقل | جودة أقل من المتوقع | Below expectations |
| `unprofessional` | 3 أو أقل | تعامل غير مناسب | Poor attitude |
| `messy` | 3 أو أقل | المكان لم يُنظَّف | Left a mess |
| `expensive` | 3 أو أقل | سعر مرتفع | Too expensive |

أي مفتاح تاني الباك بيرفضه بـ 422.

---

## 4. الإشعار لما الطلب يكتمل

العميل ممكن يوصله إشعارين: إشعار تغيّر الحالة (`type: order`) وإشعار التقييم (`type: order_rating`). شاشة التقييم تتفتح من `order_rating` فقط.

### Push data

```json
{
  "type": "order_rating",
  "model_id": "123"
}
```

`model_id` في الـ push نص (string). في `GET /api/v1/notifications` رقم (int).

### داخل قائمة الإشعارات

```json
{
  "title": "تم تنفيذ طلبك بنجاح 🎉",
  "message": "كيف كانت تجربتك؟ قيّم الخدمة في ثوانٍ",
  "type": "order_rating",
  "model_id": 123
}
```

العنوان والنص بيتغيروا حسب لغة طلب الإشعارات.

لو العميل قيّم خلاص وضغط الإشعار، افتح تفاصيل الطلب واعرض `rating` من غير ما تفتح شاشة التقييم. لو `can_rate` لسه `true`، افتح شاشة التقييم.

تذكير واحد بعد 24 ساعة لو مقيّمش، بنفس `type` و `model_id`.

---

## 5. متوسط الخدمة

`GET /api/v1/maintenances/{id}` لسه بيرجع نفس الحقول:

- `rate`
- `ratings_count`
- `my_rating`

`rate` و `ratings_count` بقوا يشملوا تقييمات الطلبات الجديدة مع التقييمات القديمة. مفيش حقل جديد مطلوب في شاشة الخدمة.

---

## 6. Cursor prompt

الصق ده في شات Cursor على مشروع التطبيق، ومعاه الملف ده:

```
اربط تقييم الطلب بالباك حسب MOBILE_ORDER_RATING_API.md.

- اعرض زرار التقييم فقط لما can_rate = true في قائمة الطلبات وتفاصيل الطلب.
- ابعت POST /api/v1/orders/{id}/rate كـ form-urlencoded: stars إجباري، و comment و tags اختياريين.
- 201: اعرض data فورًا وخلّي can_rate = false.
- 409 مع code = order_already_rated: اقفل شاشة التقييم واعرض data.
- 403 أو 422 مع code = order_not_ratable: اعرض message واعمل refresh للطلب.
- 422 من غير code: اعرض message وسيب الشاشة مفتوحة.
- الإشعار type = order_rating يفتح تفاصيل الطلب وشاشة التقييم لو can_rate = true.
- متعتمدش على POST maintenances/rate/{id}.
```
