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

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url' => 'حقل :attribute يجب أن يكون رابطاً صحيحاً.',
    'after' => 'حقل :attribute يجب أن يكون تاريخاً بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً مساوياً أو بعد :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => 'حقل :attribute يجب أن يحتوي فقط على أحرف، أرقام، شرطات، أو شرطات سفلية.',
    'alpha_num' => 'حقل :attribute يجب أن يحتوي فقط على أحرف وأرقام.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'ascii' => 'حقل :attribute يجب أن يحتوي فقط على أحرف وأرقام ورموز من نوع ASCII.',
    'before' => 'حقل :attribute يجب أن يكون تاريخاً قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً قبل أو مساوياً لـ :date.',
    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي بين :min و :max عناصر.',
        'file' => 'حقل :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max أحرف.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيحاً أو خطأ.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute يجب أن يكون تاريخاً صحيحاً.',
    'date_equals' => 'حقل :attribute يجب أن يكون تاريخاً مساوياً لـ :date.',
    'date_format' => 'حقل :attribute لا يطابق الصيغة :format.',
    'decimal' => 'حقل :attribute يجب أن يحتوي على :decimal منازل عشرية.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يتكون من :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max أرقام.',
    'email' => 'حقل :attribute يجب أن يكون بريداً إلكترونياً صحيحاً.',
    'ends_with' => 'حقل :attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لحقل :attribute غير صحيحة.',
    'exists' => 'القيمة المحددة لحقل :attribute غير صحيحة.',
    'file' => 'حقل :attribute يجب أن يكون ملفاً.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أكثر من :value عناصر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من :value أحرف.',
    ],
    'gte' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :value عناصر أو أكثر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value أحرف.',
    ],
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة في حقل :attribute غير صحيحة.',
    'integer' => 'حقل :attribute يجب أن يكون عدداً صحيحاً.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صحيحاً.',
    'ipv4' => 'حقل :attribute يجب أن يكون عنوان IPv4 صحيحاً.',
    'ipv6' => 'حقل :attribute يجب أن يكون عنوان IPv6 صحيحاً.',
    'json' => 'حقل :attribute يجب أن يكون نص JSON صحيح.',
    'lowercase' => 'حقل :attribute يجب أن يكون بأحرف صغيرة.',
    'lt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أقل من :value عناصر.',
        'file' => 'حقل :attribute يجب أن يكون أقل من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من :value.',
        'string' => 'حقل :attribute يجب أن يكون أقل من :value أحرف.',
    ],
    'lte' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :value عناصر.',
        'file' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value.',
        'string' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value أحرف.',
    ],
    'max' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max عناصر.',
        'file' => 'حقل :attribute يجب ألا يكون أكبر من :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب ألا يكون أكبر من :max.',
        'string' => 'حقل :attribute يجب ألا يكون أكبر من :max أحرف.',
    ],
    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على الأقل :min عناصر.',
        'file' => 'حقل :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون على الأقل :min.',
        'string' => 'حقل :attribute يجب أن يكون على الأقل :min أحرف.',
    ],
    'not_in' => 'القيمة المحددة في حقل :attribute غير صحيحة.',
    'not_regex' => 'صيغة حقل :attribute غير صحيحة.',
    'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
    'required' => 'حقل :attribute مطلوب.',
    'same' => 'حقل :attribute يجب أن يطابق :other.',
    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي :size عناصر.',
        'file' => 'حقل :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون :size.',
        'string' => 'حقل :attribute يجب أن يكون :size أحرف.',
    ],
    'string' => 'حقل :attribute يجب أن يكون نصاً.',
    'unique' => 'قيمة :attribute مستخدمة بالفعل.',
    'uploaded' => 'فشل في رفع :attribute.',
    'url' => 'حقل :attribute يجب أن يكون رابطاً صحيحاً.',
    'uuid' => 'حقل :attribute يجب أن يكون UUID صحيحاً.',

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

    'attributes' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    'validation_failed' => 'فشل التحقق من البيانات المرسلة.',

];
