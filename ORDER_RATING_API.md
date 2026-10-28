# تقييم الطلب (تقييم الخدمة) — المطلوب من الباك إند

> التطبيق جاهز بالكامل. أول ما الـ API ده يتنفذ بالشكل الموضح هنا، التقييم هيظهر للعميل في التطبيق فوراً بدون أي تحديث للتطبيق.

---

## 1. الفكرة باختصار

- لما الطلب **يكتمل**، العميل يقدر يقيّم الخدمة اللي اتقدمت له في الطلب ده.
- التقييم: **من 1 لـ 5 نجوم (إجباري)** + **تاجات سريعة (اختياري)** + **تعليق (اختياري)**.
- **كل طلب يتقيّم مرة واحدة بس**، ومفيش تعديل ولا حذف من العميل.
- بعد التقييم، العميل يشوف تقييمه في قائمة الطلبات وفي تفاصيل الطلب.

التطبيق **مش بيحدد بنفسه** إمتى يظهر زرار التقييم — الباك هو اللي بيقرر عن طريق حقل `can_rate`. عشان كده أي قاعدة (زي مدة سماح للتقييم) تتغير من الباك بس.

---

## 2. المطلوب بالظبط (Checklist)

- [ ] جدول `order_ratings` (الهيكل في البند 3).
- [ ] إضافة حقلين `can_rate` و `rating` لكائن الطلب في `GET orders` و `GET orders/{id}` (البند 4).
- [ ] Endpoint جديد: `POST orders/{id}/rate` (البند 5).
- [ ] إشعار للعميل لما الطلب يكتمل بنوع `order_rating` (البند 7) — مستحسن جداً.
- [ ] عرض التقييمات في لوحة التحكم (البند 8) — مستحسن.

---

## 3. قاعدة البيانات

### جدول `order_ratings`

| العمود | النوع | ملاحظات |
|---|---|---|
| `id` | bigint, PK | |
| `order_id` | bigint, FK → orders.id | **UNIQUE** — ده اللي بيضمن إن الطلب يتقيّم مرة واحدة حتى لو جه طلبين في نفس اللحظة |
| `user_id` | bigint, FK → users.id | العميل اللي قيّم |
| `maintenance_id` | bigint, nullable | الخدمة الخاصة بالطلب (لو موجودة) — لحساب متوسط تقييم الخدمة |
| `stars` | tinyint unsigned | من 1 لـ 5 |
| `comment` | text, nullable | أقصى حد 500 حرف |
| `tags` | json, nullable | مصفوفة مفاتيح، مثلاً `["on_time","quality"]` |
| `created_at` / `updated_at` | timestamps | |

```php
Schema::create('order_ratings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->unsignedBigInteger('maintenance_id')->nullable()->index();
    $table->unsignedTinyInteger('stars');
    $table->text('comment')->nullable();
    $table->json('tags')->nullable();
    $table->timestamps();
});
```

```php
// Order.php
public function rating() { return $this->hasOne(OrderRating::class); }

// OrderRating.php
protected $fillable = ['order_id', 'user_id', 'maintenance_id', 'stars', 'comment', 'tags'];
protected $casts = ['tags' => 'array'];
```

---

## 4. التعديل على كائن الطلب (Order Resource)

في **كل** مكان بيرجع فيه طلب للعميل:
- `GET orders?status=current|completed|cancelled&page=` (القائمة)
- `GET orders/{id}` (التفاصيل)

ضيف الحقلين دول:

| الحقل | النوع | المعنى |
|---|---|---|
| `can_rate` | `bool` | `true` **فقط** لو: الطلب مكتمل + لسه متقيّمش + تبع العميل الحالي (+ جوه مدة السماح لو عايزين مدة). غير كده `false` |
| `rating` | `object` أو `null` | تقييم العميل لو قيّم، و `null` لو لسه |

### مثال — طلب مكتمل ولسه متقيّمش

```json
{
  "id": 123,
  "status": "completed",
  "status_label": "مكتمل",
  "...": "باقي الحقول الحالية زي ما هي",
  "can_rate": true,
  "rating": null
}
```

### مثال — طلب اتقيّم

```json
{
  "id": 123,
  "status": "completed",
  "status_label": "مكتمل",
  "...": "باقي الحقول الحالية زي ما هي",
  "can_rate": false,
  "rating": {
    "id": 31,
    "stars": 4,
    "comment": "الفني محترم وشغله نضيف",
    "tags": ["on_time", "quality"],
    "created_at": "2026-10-27T14:30:00+03:00"
  }
}
```

### شكل كائن `rating` (ثابت في كل الأماكن)

| الحقل | النوع | ملاحظات |
|---|---|---|
| `id` | int | |
| `stars` | int | 1–5 |
| `comment` | string أو null | |
| `tags` | array of strings | `[]` لو مفيش |
| `created_at` | string | **ISO 8601** يفضل بالـ timezone (`2026-10-27T14:30:00+03:00`). التطبيق بيعرض التاريخ بلغة المستخدم |

```php
// OrderResource::toArray
'can_rate' => $this->canBeRatedBy($request->user()),
'rating'   => $this->rating ? [
    'id'         => $this->rating->id,
    'stars'      => (int) $this->rating->stars,
    'comment'    => $this->rating->comment,
    'tags'       => $this->rating->tags ?? [],
    'created_at' => $this->rating->created_at->toIso8601String(),
] : null,
```

```php
// Order.php
public function canBeRatedBy(?User $user): bool
{
    return $user
        && $this->user_id === $user->id
        && $this->status === 'completed'   // غيّرها لقيمة الحالة المكتملة عندكم
        && $this->rating === null;         // بيستخدم الـ relation المحمّلة بـ with('rating')
        // اختياري (مدة سماح): && $this->completed_at?->gt(now()->subDays(30))
}
```

> ⚠️ **Performance:** في القائمة استخدم `->with('rating')` عشان متعملش query لكل طلب (N+1).

> ℹ️ لو `can_rate` مش موجود في الريسبونس، التطبيق بيعتبره `false` ومش هيظهر زرار التقييم. يعني ده الحقل اللي "بيشغّل" الميزة.

---

## 5. Endpoint التقييم

```
POST /api/v1/orders/{id}/rate
```

### Headers (نفس باقي الـ API)

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/x-www-form-urlencoded
lang: ar | en
Accept-Language: ar | en
```

### Body (form-urlencoded)

| الحقل | النوع | إجباري؟ | القواعد |
|---|---|---|---|
| `stars` | int | ✅ | `required|integer|between:1,5` |
| `comment` | string | ❌ | `nullable|string|max:500` — التطبيق مش بيبعته لو فاضي |
| `tags[]` | array of strings | ❌ | `nullable|array|max:10`، وكل عنصر `in:` القائمة في البند 6 — التطبيق مش بيبعته لو فاضي |

شكل الـ body اللي التطبيق بيبعته فعلياً:

```
stars=4&comment=الفني محترم&tags[0]=on_time&tags[1]=quality
```

(Laravel بيقرأ `tags[0]`, `tags[1]` كـ array عادي.)

### المنطق المطلوب بالترتيب

1. الطلب مش موجود → **404**.
2. الطلب مش تبع العميل → **403** + `code: "order_not_ratable"`.
3. الطلب اتقيّم قبل كده → **409** + `code: "order_already_rated"` + رجّع التقييم الموجود في `data`.
4. الطلب مش مكتمل (أو عدّى مدة السماح) → **422** + `code: "order_not_ratable"`.
5. Validation غلط → **422** + `message` + `errors`.
6. احفظ التقييم (جوه transaction) → **201**.

> مهم: لو جه طلبين في نفس الوقت، الـ UNIQUE على `order_id` هيرمي `QueryException` (duplicate). امسكها ورجّع نفس رد الـ 409.

### ✅ Success — `201 Created`

```json
{
  "status": true,
  "message": "شكراً لتقييمك!",
  "data": {
    "id": 31,
    "stars": 4,
    "comment": "الفني محترم وشغله نضيف",
    "tags": ["on_time", "quality"],
    "created_at": "2026-10-27T14:30:00+03:00"
  }
}
```

`data` = كائن التقييم نفسه بنفس الشكل اللي في البند 4. التطبيق بيعرضه فوراً.

### ❌ اتقيّم قبل كده — `409 Conflict`

```json
{
  "status": false,
  "code": "order_already_rated",
  "message": "لقد قمت بتقييم هذا الطلب مسبقاً",
  "data": {
    "id": 31,
    "stars": 5,
    "comment": null,
    "tags": [],
    "created_at": "2026-10-26T10:00:00+03:00"
  }
}
```

التطبيق هيعرض الرسالة، يقفل شاشة التقييم، ويعرض التقييم اللي في `data` (حالة إن العميل قيّم من جهاز تاني).

### ❌ مش مسموح — `403` أو `422`

```json
{
  "status": false,
  "code": "order_not_ratable",
  "message": "لا يمكن تقييم هذا الطلب"
}
```

التطبيق هيعرض الرسالة ويعيد تحميل الطلب عشان يخفي زرار التقييم.

### ❌ Validation — `422`

```json
{
  "status": false,
  "message": "التقييم يجب أن يكون بين 1 و 5",
  "errors": { "stars": ["التقييم يجب أن يكون بين 1 و 5"] }
}
```

التطبيق هيعرض `message` ويسيب العميل يعدّل ويبعت تاني.

### قواعد الرد (مهمة للتطبيق)

- لازم `status` (bool) يكون موجود في **كل** رد — التطبيق بيعتمد عليه.
- `message` تتبعت **مترجمة** حسب `lang` / `Accept-Language`.
- `code` يتكتب **بالظبط** زي ما هو: `order_already_rated` و `order_not_ratable`.

### مثال Controller

```php
// routes/api.php (جوه v1 + auth:sanctum)
Route::post('orders/{order}/rate', [OrderRatingController::class, 'store']);
```

```php
class OrderRatingController extends Controller
{
    public const TAGS = [
        'on_time', 'quality', 'professional', 'clean', 'fair_price',
        'late', 'poor_quality', 'unprofessional', 'messy', 'expensive',
    ];

    public function store(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            return $this->notRatable(403);
        }
        if ($order->rating) {
            return $this->alreadyRated($order->rating);
        }
        if (! $order->canBeRatedBy($user)) {
            return $this->notRatable(422);
        }

        $data = $request->validate([
            'stars'   => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
            'tags'    => 'nullable|array|max:10',
            'tags.*'  => ['string', Rule::in(self::TAGS)],
        ]);

        try {
            $rating = DB::transaction(fn () => $order->rating()->create([
                'user_id'        => $user->id,
                'maintenance_id' => $order->maintenance_id,
                'stars'          => $data['stars'],
                'comment'        => $data['comment'] ?? null,
                'tags'           => array_values(array_unique($data['tags'] ?? [])),
            ]));
        } catch (QueryException $e) {
            // طلبين في نفس اللحظة → الـ unique index منع التاني
            return $this->alreadyRated($order->fresh()->rating);
        }

        // اختياري: تحديث متوسط تقييم الخدمة (البند 8)

        return response()->json([
            'status'  => true,
            'message' => __('ratings.thanks'),
            'data'    => $this->ratingPayload($rating),
        ], 201);
    }

    private function alreadyRated(?OrderRating $rating)
    {
        return response()->json([
            'status'  => false,
            'code'    => 'order_already_rated',
            'message' => __('ratings.already_rated'),
            'data'    => $rating ? $this->ratingPayload($rating) : null,
        ], 409);
    }

    private function notRatable(int $http)
    {
        return response()->json([
            'status'  => false,
            'code'    => 'order_not_ratable',
            'message' => __('ratings.not_ratable'),
        ], $http);
    }

    private function ratingPayload(OrderRating $r): array
    {
        return [
            'id'         => $r->id,
            'stars'      => (int) $r->stars,
            'comment'    => $r->comment,
            'tags'       => $r->tags ?? [],
            'created_at' => $r->created_at->toIso8601String(),
        ];
    }
}
```

رسائل الترجمة المقترحة:

| key | ar | en |
|---|---|---|
| `ratings.thanks` | شكراً لتقييمك! | Thanks for your rating! |
| `ratings.already_rated` | لقد قمت بتقييم هذا الطلب مسبقاً | You have already rated this order |
| `ratings.not_ratable` | لا يمكن تقييم هذا الطلب | This order can't be rated |

---

## 6. التاجات (Tags)

التطبيق بيعرض تاجات إيجابية لو التقييم 4 أو 5، وتاجات "محتاجة تحسين" لو 3 أو أقل. الباك بيخزن **المفتاح** بس. المفاتيح دي **ثابتة ومتتغيرش**.

| المفتاح | النوع | العربي | English |
|---|---|---|---|
| `on_time` | إيجابي | الالتزام بالموعد | On time |
| `quality` | إيجابي | جودة التنفيذ | Quality work |
| `professional` | إيجابي | احترافية الفريق | Professional team |
| `clean` | إيجابي | نظافة المكان | Left it clean |
| `fair_price` | إيجابي | سعر مناسب | Fair price |
| `late` | سلبي | تأخير في الموعد | Arrived late |
| `poor_quality` | سلبي | جودة أقل من المتوقع | Below expectations |
| `unprofessional` | سلبي | تعامل غير مناسب | Poor attitude |
| `messy` | سلبي | المكان لم يُنظَّف | Left a mess |
| `expensive` | سلبي | سعر مرتفع | Too expensive |

لو حبيتوا تضيفوا تاج جديد بعدين، لازم يتضاف في التطبيق كمان (`RatingTag` في `lib/features/order_rating/domain/model/order_rating_model.dart`). أي مفتاح التطبيق ميعرفوش هيتخزن عادي بس مش هيتعرض.

---

## 7. الإشعار لما الطلب يكتمل (مستحسن جداً)

لما حالة الطلب تتغير لـ **مكتمل**، ابعت للعميل إشعار (Push + يتسجل في الإشعارات). التطبيق جاهز له: لما العميل يضغط عليه، بيفتح تفاصيل الطلب **وشاشة التقييم مفتوحة تلقائياً**.

### FCM data payload

```json
{
  "notification": {
    "title": "تم تنفيذ طلبك بنجاح 🎉",
    "body": "كيف كانت تجربتك؟ قيّم الخدمة في ثوانٍ"
  },
  "data": {
    "type": "order_rating",
    "model_id": "123"
  }
}
```

- `type` لازم يكون `order_rating` بالظبط.
- `model_id` = رقم الطلب (string).

### في جدول الإشعارات (`GET notifications`)

نفس الشكل الحالي، بس جوه `data`:

```json
"data": {
  "title": "تم تنفيذ طلبك بنجاح 🎉",
  "message": "كيف كانت تجربتك؟ قيّم الخدمة في ثوانٍ",
  "type": "order_rating",
  "model_id": 123
}
```

### تذكير (اختياري)

لو العميل مقيّمش خلال 24 ساعة، ابعت **تذكير واحد بس** بنفس الـ payload. متبعتش أكتر من كده عشان العميل ميتضايقش.

> لو العميل ضغط على الإشعار وكان قيّم خلاص، التطبيق هيفتح التفاصيل ويعرض تقييمه عادي من غير ما يفتح شاشة التقييم.

---

## 8. لوحة التحكم (مستحسن)

- صفحة **التقييمات**: رقم الطلب، العميل، الخدمة، النجوم، التاجات (بالعربي من جدول البند 6)، التعليق، التاريخ.
- فلترة بالنجوم وبالخدمة وبالتاريخ.
- تمييز التقييمات **3 نجوم أو أقل** (لون أحمر أو تنبيه للإدارة) عشان يتم التواصل مع العميل.
- **متوسط التقييم لكل خدمة** (`maintenance_id`) وعدد التقييمات — ممكن يتحسب live أو يتخزن في جدول الخدمات (`rating_avg`, `rating_count`) ويتحدث بعد كل تقييم.
- إخفاء تعليق مسيء من العرض العام (لو التعليقات هتظهر لعملاء تانيين) بدون حذفه.

---

## 9. سيناريوهات الاختبار

| # | السيناريو | المتوقع |
|---|---|---|
| 1 | طلب جاري | `can_rate: false`, `rating: null` — مفيش أي حاجة تقييم في التطبيق |
| 2 | طلب مكتمل ولسه متقيّمش | `can_rate: true`, `rating: null` — يظهر كارت التقييم في التفاصيل وشريط النجوم في القائمة |
| 3 | تقييم 5 نجوم + تاجين + تعليق | `201` — يظهر "شكراً لك" ثم كارت "تقييمك للخدمة" |
| 4 | تقييم بنجوم بس (من غير تعليق وتاجات) | `201` و `comment: null`, `tags: []` |
| 5 | تقييم نفس الطلب تاني | `409` + `order_already_rated` + التقييم القديم في `data` |
| 6 | طلبين تقييم في نفس اللحظة | واحد `201` والتاني `409` (الـ unique index) |
| 7 | تقييم طلب عميل تاني | `403` + `order_not_ratable` |
| 8 | تقييم طلب لسه مش مكتمل | `422` + `order_not_ratable` |
| 9 | `stars=0` أو `stars=6` أو من غيره | `422` validation |
| 10 | تاج مش في القائمة | `422` validation |
| 11 | تعليق أكتر من 500 حرف | `422` validation |
| 12 | `GET orders/{id}` بعد التقييم | `can_rate: false` + `rating` كامل |
| 13 | `GET orders?status=completed` | كل طلب فيه `can_rate` و `rating` (بدون N+1) |
| 14 | `lang: en` | كل الرسائل بالإنجليزي |
| 15 | الطلب يكتمل | يوصل إشعار `order_rating` + الضغط عليه يفتح شاشة التقييم |

### cURL سريع

```bash
curl -X POST "https://aluminumpro.net/api/v1/orders/123/rate" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json" \
  -H "lang: ar" \
  --data-urlencode "stars=4" \
  --data-urlencode "comment=الفني محترم وشغله نضيف" \
  --data-urlencode "tags[0]=on_time" \
  --data-urlencode "tags[1]=quality"
```

---

## 10. ملاحظة عن الـ endpoint القديم

فيه endpoint قديم `POST maintenances/rate/{id}` بيقيّم الخدمة نفسها من غير ربط بطلب ومن غير منع للتكرار. التقييم الجديد مربوط **بالطلب** وبيتحسب في متوسط الخدمة عن طريق `maintenance_id`. التطبيق مش بيستخدم القديم في أي مكان حالياً.
