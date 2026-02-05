<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول الحقل :attribute.',
    'accepted_if' => 'يجب قبول الحقل :attribute عندما يكون :other مساوياً لـ :value.',
    'active_url' => 'الحقل :attribute ليس رابطاً صحيحاً.',
    'after' => 'يجب أن يكون الحقل :attribute تاريخاً لاحقاً للتاريخ :date.',
    'after_or_equal' => 'يجب أن يكون الحقل :attribute تاريخاً لاحقاً أو مساوياً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي الحقل :attribute سوى على حروف.',
    'alpha_dash' => 'يجب أن لا يحتوي الحقل :attribute سوى على حروف وأرقام ومطّات وشَرطات سفلية.',
    'alpha_num' => 'يجب أن لا يحتوي الحقل :attribute سوى على حروف وأرقام.',
    'array' => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي الحقل :attribute على رموز وحروف وأرقام إنجليزية فقط.',
    'before' => 'يجب أن يكون الحقل :attribute تاريخاً سابقاً للتاريخ :date.',
    'before_or_equal' => 'يجب أن يكون الحقل :attribute تاريخاً سابقاً أو مساوياً للتاريخ :date.',
    'between' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على عدد من العناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون عدد حروف الحقل :attribute بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة الحقل :attribute إما true أو false.',
    'can' => 'يحتوي الحقل :attribute على قيمة غير مسموح بها.',
    'confirmed' => 'تأكيد الحقل :attribute غير متطابق.',
    'contains' => 'الحقل :attribute يفتقد إلى قيمة مطلوبة.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'الحقل :attribute ليس تاريخاً صحيحاً.',
    'date_equals' => 'يجب أن يكون الحقل :attribute تاريخاً مساوياً للتاريخ :date.',
    'date_format' => 'لا يتوافق الحقل :attribute مع الشكل :format.',
    'decimal' => 'يجب أن يحتوي الحقل :attribute على :decimal خانات عشرية.',
    'declined' => 'يجب رفض الحقل :attribute.',
    'declined_if' => 'يجب رفض الحقل :attribute عندما يكون :other مساوياً لـ :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits' => 'يجب أن يحتوي الحقل :attribute على :digits أرقام.',
    'digits_between' => 'يجب أن يحتوي الحقل :attribute على عدد من الأرقام بين :min و :max.',
    'dimensions' => 'الحقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مكررة.',
    'doesnt_end_with' => 'يجب ألا ينتهي الحقل :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ الحقل :attribute بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون الحقل :attribute عنوان بريد إلكتروني صحيح.',
    'ends_with' => 'يجب أن ينتهي الحقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'قيمة الحقل :attribute المختارة غير صالحة.',
    'exists' => 'قيمة الحقل :attribute المختارة غير صالحة.',
    'extensions' => 'يجب أن يكون امتداد الحقل :attribute أحد الامتدادات التالية: :values.',
    'file' => 'يجب أن يكون الحقل :attribute ملفاً.',
    'filled' => 'الحقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول النّص :attribute أكثر من :value حروف/حرف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :value عُنصراً.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أكبر من :value.',
        'string' => 'يجب أن يكون طول النّص :attribute على الأقل :value حروف/حرف.',
    ],
    'hex_color' => 'يجب أن يكون الحقل :attribute لوناً بنظام hex صالحاً.',
    'image' => 'يجب أن يكون الحقل :attribute صورة.',
    'in' => 'قيمة الحقل :attribute المختارة غير صالحة.',
    'in_array' => 'الحقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون الحقل :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون الحقل :attribute عنوان IP صحيحاً.',
    'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 صحيحاً.',
    'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 صحيحاً.',
    'json' => 'يجب أن يكون الحقل :attribute نصاً من نوع JSON.',
    'list' => 'يجب أن يكون الحقل :attribute قائمة.',
    'lowercase' => 'يجب أن يكون الحقل :attribute حروفاً صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أقل من :value عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute أصغر من :value.',
        'string' => 'يجب أن يكون طول النّص :attribute أقل من :value حروف/حرف.',
    ],
    'lte' => [
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أصغر من :value.',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :value حروف/حرف.',
    ],
    'mac_address' => 'يجب أن يكون الحقل :attribute عنوان MAC صحيحاً.',
    'max' => [
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :max عناصر.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب أن لا تتجاوز قيمة الحقل :attribute :max.',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :max حروف/حرف.',
    ],
    'max_digits' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون الحقل :attribute ملفاً من نوع: :values.',
    'mimetypes' => 'يجب أن يكون الحقل :attribute ملفاً من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min عُنصراً.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أكبر من :min.',
        'string' => 'يجب أن يكون طول النّص :attribute على الأقل :min حروف/حرف.',
    ],
    'min_digits' => 'يجب أن يحتوي الحقل :attribute على :min أرقام على الأقل.',
    'missing' => 'يجب أن يكون الحقل :attribute مفقوداً.',
    'missing_if' => 'يجب أن يكون الحقل :attribute مفقوداً عندما يكون :other مساوياً لـ :value.',
    'missing_unless' => 'يجب أن يكون الحقل :attribute مفقوداً إلا إذا كان :other مساوياً لـ :value.',
    'missing_with' => 'يجب أن يكون الحقل :attribute مفقوداً عندما يكون :values موجوداً.',
    'missing_with_all' => 'يجب أن يكون الحقل :attribute مفقوداً عندما تكون :values موجودة.',
    'multiple_of' => 'يجب أن يكون الحقل :attribute من مضاعفات :value.',
    'not_in' => 'قيمة الحقل :attribute المختارة غير صالحة.',
    'not_regex' => 'صيغة الحقل :attribute غير صحيحة.',
    'numeric' => 'يجب أن يكون الحقل :attribute رقماً.',
    'password' => [
        'letters' => 'يجب أن يحتوي الحقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي الحقل :attribute على حرف كبير وحرف صغير واحد على الأقل.',
        'numbers' => 'يجب أن يحتوي الحقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي الحقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'القيمة المدخلة في الحقل :attribute ظهرت في تسريب بيانات. الرجاء اختيار قيمة مختلفة.',
    ],
    'present' => 'يجب تقديم الحقل :attribute.',
    'present_if' => 'يجب تقديم الحقل :attribute عندما يكون :other مساوياً لـ :value.',
    'present_unless' => 'يجب تقديم الحقل :attribute إلا إذا كان :other مساوياً لـ :value.',
    'present_with' => 'يجب تقديم الحقل :attribute عندما يكون :values موجوداً.',
    'present_with_all' => 'يجب تقديم الحقل :attribute عندما تكون :values موجودة.',
    'prohibited' => 'الحقل :attribute محظور.',
    'prohibited_if' => 'الحقل :attribute محظور عندما يكون :other مساوياً لـ :value.',
    'prohibited_if_accepted' => 'الحقل :attribute محظور عندما يكون :other مقبولاً.',
    'prohibited_if_declined' => 'الحقل :attribute محظور عندما يكون :other مرفوضاً.',
    'prohibited_unless' => 'الحقل :attribute محظور إلا إذا كان :other في :values.',
    'prohibits' => 'الحقل :attribute يحظر وجود :other.',
    'regex' => 'صيغة الحقل :attribute غير صحيحة.',
    'required' => 'الحقل :attribute مطلوب.',
    'required_array_keys' => 'الحقل :attribute يجب أن يحتوي على مدخلات لـ: :values.',
    'required_if' => 'الحقل :attribute مطلوب عندما يكون :other مساوياً لـ :value.',
    'required_if_accepted' => 'الحقل :attribute مطلوب عندما يكون :other مقبولاً.',
    'required_if_declined' => 'الحقل :attribute مطلوب عندما يكون :other مرفوضاً.',
    'required_unless' => 'الحقل :attribute مطلوب إلا إذا كان :other موجوداً في :values.',
    'required_with' => 'الحقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all' => 'الحقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'الحقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'الحقل :attribute مطلوب عندما لا يكون أي من :values موجوداً.',
    'same' => 'يجب أن يتطابق الحقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :size عنصراً.',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يكون طول النّص :attribute :size حروف/حرف.',
    ],
    'starts_with' => 'يجب أن يبدأ الحقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون الحقل :attribute نصاً.',
    'timezone' => 'يجب أن يكون الحقل :attribute نطاقاً زمنياً صحيحاً.',
    'unique' => 'قيمة الحقل :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل تحميل الحقل :attribute.',
    'uppercase' => 'يجب أن يكون الحقل :attribute حروفاً كبيرة.',
    'url' => 'صيغة الرابط :attribute غير صحيحة.',
    'ulid' => 'يجب أن يكون الحقل :attribute ULID صحيحاً.',
    'uuid' => 'يجب أن يكون الحقل :attribute UUID صحيحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'service_id' => [
            'required' => 'الخدمة مطلوبة.',
            'exists' => 'الخدمة غير موجودة أو غير مفعلة.',
        ],
        'latitude' => [
            'required' => 'خط العرض مطلوب.',
            'numeric' => 'يجب أن يكون خط العرض رقمًا.',
        ],
        'longitude' => [
            'required' => 'خط الطول مطلوب.',
            'numeric' => 'يجب أن يكون خط الطول رقمًا.',
        ],
        'location_name' => [
            'required' => 'اسم الموقع مطلوب.',
            'string' => 'يجب أن يكون اسم الموقع نصًا.',
            'max' => 'اسم الموقع لا يجب أن يزيد عن 255 حرفًا.',
        ],
        'description' => [
            'required' => 'الوصف مطلوب.',
            'string' => 'يجب أن يكون الوصف نصًا.',
            'max' => 'الوصف لا يجب أن يزيد عن 1000 حرف.',
        ],
        'internal_note' => [
            'string' => 'يجب أن تكون الملاحظة الداخلية نصًا.',
            'max' => 'الملاحظة لا يجب أن تزيد عن 1000 حرف.',
        ],
        'images' => [
            'array' => 'يجب أن تكون الصور في شكل مصفوفة.',
        ],
        'images.*' => [
            'mimes' => 'كل صورة يجب أن تكون من نوع: jpg، jpeg، png، gif، webp، avi، mkv.',
            'max' => 'حجم كل صورة لا يجب أن يزيد عن 10 ميجا.',
        ],
        'sounds' => [
            'array' => 'يجب أن تكون التسجيلات الصوتية في شكل مصفوفة.',
        ],
        'sounds.*' => [
            'mimes' => 'كل تسجيل صوتي يجب أن يكون من نوع: mp3، wav، ogg، m4a.',
            'max' => 'حجم كل تسجيل لا يجب أن يزيد عن 10 ميجا.',
        ],
        'paymentmethod' => [
            'required' => 'طريقة الدفع مطلوبة.',
            'integer' => 'يجب أن تكون طريقة الدفع رقمًا.',
            'in' => 'طريقة الدفع المحددة غير صالحة.',
        ],
        'user_name' => [
            'string' => 'يجب أن يكون اسم المستخدم نصًا.',
            'max' => 'اسم المستخدم لا يجب أن يزيد عن 255 حرفًا.',
        ],
        'rating' => [
            'required' => 'حقل التقييم مطلوب.',
            'integer' => 'يجب أن يكون التقييم رقمًا.',
            'min' => 'أقل تقييم مسموح به هو 1.',
            'max' => 'أقصى تقييم مسموح به هو 5.',
        ],
        'mobile' => [
            'required' => 'رقم الجوال مطلوب.',
            'regex' => 'صيغة رقم الجوال غير صحيحة، يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'max' => 'رقم الجوال لا يجب أن يتجاوز 15 رقمًا.',
            'string' => 'يجب أن يكون رقم الجوال نصًا.',
            'unique' => 'رقم الجوال مستخدم بالفعل.',
            'exists' => 'رقم الجوال غير مسجل لدينا.',
        ],
        'uuid' => [
            'required' => 'معرّف الجهاز مطلوب.',
            'string' => 'يجب أن يكون معرف الجهاز نصًا.',
        ],
        'device_token' => [
            'required' => 'رمز الجهاز مطلوب.',
        ],
        'device_type' => [
            'required' => 'نوع الجهاز مطلوب.',
            'string' => 'نوع الجهاز يجب أن يكون نصًا.',
        ],
        'name' => [
            'required' => 'الاسم مطلوب.',
            'string' => 'يجب أن يكون الاسم نصًا.',
            'max' => 'الاسم لا يجب أن يتجاوز 25 حرفًا.',
            'min' => 'الاسم يجب أن يكون على الأقل حرفين.',
        ],
        'code' => [
            'required' => 'رمز التحقق مطلوب.',
            'string' => 'رمز التحقق يجب أن يكون نصًا.',
            'digits' => 'رمز التحقق يجب أن يكون مكونًا من 4 أرقام.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'service_id' => 'الخدمة',
        'paymentmethod' => 'طريقة الدفع',
        'user_name' => 'اسم المستخدم',
        'rating' => 'التقييم',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'mobile' => 'رقم الهاتف',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'login' => 'البريد الإلكتروني أو رقم الهاتف',
        'uuid' => 'معرف الجهاز',
        'device_token' => 'رمز الجهاز',
        'device_type' => 'نوع الجهاز',
        'otp' => 'رمز التحقق',
        'code' => 'رمز التحقق',
    ],
    
    // Additional custom keys from original file
    'mobile_required' => 'رقم الجوال مطلوب.',
    'mobile_exists' => 'رقم الجوال غير مسجل.',
    'uuid_required' => 'معرف الجهاز مطلوب.',
    'uuid_string' => 'معرف الجهاز يجب أن يكون نصاً.',
    'device_token_required' => 'رمز الجهاز مطلوب.',
    'device_type_required' => 'نوع الجهاز مطلوب.',
    'device_type_string' => 'نوع الجهاز يجب أن يكون نصاً.',
    'Activation code is not correct'=>'كود التفعيل غير صحيح',
    'Your account activated successfully'=>'تم تفعيل حسابك بنجاح',
    'Activation code is expired'=>'تم انتهاء صلاحية كود التفعيل',
    'code_required' => 'رمز التحقق مطلوب.',
    'code_digits' => 'رمز التحقق يجب أن يتكون من 4 أرقام.',
    
    // Added missing mobile_format
    'mobile_format' => 'رقم الهاتف يجب أن يبدأ بـ 05 ويتبعه 8 أرقام',

];
