<?php

return [

    'organisation' => [
        'short_name' => 'MAKU Union',
        'name_hi' => 'मिर्जापुर असंगठित कामगार यूनियन',
        'affiliation_hi' => 'संबद्ध: भारतीय मजदूर संघ (BMS)',
        'district' => 'मिर्जापुर',
        'state' => 'उत्तर प्रदेश',
    ],

    'uploads' => [
        'disk' => env('MAKU_UPLOAD_DISK', 'public'),
        'max_kb' => (int) env('MAKU_UPLOAD_MAX_KB', 4096),
    ],

    'membership' => [
        'prefix' => 'MAKU',
        'fee' => (float) env('MAKU_MEMBERSHIP_FEE', 100),
        'renewal_fee' => (float) env('MAKU_RENEWAL_FEE', 100),
        'duration_years' => 1,
    ],

    'grievance' => [
        'prefix' => 'MAKU-GRV',
        'max_documents' => 5,
        'max_document_kb' => 4096,
    ],

    'payment' => [
        'gateway' => env('MAKU_PAYMENT_GATEWAY', 'sandbox'),
        'currency' => 'INR',
        'fee_percent' => (float) env('MAKU_GATEWAY_FEE_PERCENT', 2),
        'gst_percent' => (float) env('MAKU_GATEWAY_GST_PERCENT', 18),
        'cashfree' => [
            'app_id' => env('CASHFREE_APP_ID'),
            'secret_key' => env('CASHFREE_SECRET_KEY'),
            'env' => env('CASHFREE_ENV', 'sandbox'),
            'api_version' => env('CASHFREE_API_VERSION', '2023-08-01'),
        ],
    ],

    'translate' => [
        'enabled' => (bool) env('GOOGLE_TRANSLATE_ENABLED', false),
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY'),
        'project_id' => env('GOOGLE_TRANSLATE_PROJECT_ID'),
        'monthly_limit' => (int) env('GOOGLE_TRANSLATE_MONTHLY_LIMIT', 500),
        'cache_ttl' => 60 * 60 * 24 * 30,
        'target' => 'hi',
    ],

    'pagination' => [
        'public' => 9,
        'admin' => 15,
    ],

];
