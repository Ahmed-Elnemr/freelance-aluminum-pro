<?php

return [
    // Auth Messages
    'auth' => [
        'login' => 'تسجيل الدخول',
        'register' => 'تسجيل حساب جديد',
        'logout' => 'تسجيل الخروج',
        'profile' => 'الملف الشخصي',
    ],

    // Validation Messages
    'validation' => [
        'mobile' => [
            'required' => 'يرجى إدخال رقم الجوال',
            'format' => 'يجب أن يبدأ رقم الجوال بـ 05 متبوعاً بـ 8 أرقام',
            'max' => 'رقم الجوال طويل جداً',
            'unique' => 'رقم الجوال مسجل مسبقاً'
        ],
        'name' => [
            'required' => 'يرجى إدخال الاسم',
            'string' => 'يجب أن يكون الاسم نصاً',
            'max' => 'يجب ألا يتجاوز الاسم 25 حرفاً',
            'min' => 'يجب أن يكون الاسم 5 أحرف على الأقل',
            'format' => 'يجب أن يحتوي الاسم على حروف عربية أو إنجليزية ومسافات فقط'
        ]
    ],

    // Response Messages
    'responses' => [
        'success' => 'نجاح',
        'error' => 'خطأ',
        'warning' => 'تنبيه',
        'info' => 'معلومات',
        'verification_sent' => 'تم إرسال رمز التحقق إلى رقم جوالك',
        'invalid_code' => 'رمز التحقق غير صحيح',
        'enter_code' => 'يرجى إدخال رمز التحقق'
    ],

    // Common Actions
    'actions' => [
        'save' => 'حفظ التغييرات',
        'cancel' => 'إلغاء',
        'back' => 'رجوع',
        'next' => 'التالي',
        'submit' => 'إرسال'
    ]


    
];
