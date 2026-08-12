<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Features
    |--------------------------------------------------------------------------
    |
    | Shared features included across all subscription packages.
    |
    */
    'features' => [
        'Facebook Page (unlimited)',
        'Instagram (unlimited)',
        'WhatsApp (unlimited)',
        'Messages (unlimited)',
        'Comments (unlimited)',
        'Others (unlimited)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Trial Period
    |--------------------------------------------------------------------------
    |
    | Initial trial duration (in days) granted on first registration.
    |
    */
    'trial_period_days' => 21,

    /*
    |--------------------------------------------------------------------------
    | HotashPay Payment Gateway
    |--------------------------------------------------------------------------
    |
    | API Base URL and Bearer API Key for HotashPay integration.
    |
    */
    'hotashpay' => [
        'base_url' => env('HOTASHPAY_BASE_URL', 'https://pay.hotash.tech'),
        'api_key' => env('HOTASHPAY_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Available subscription packages with pricing and duration intervals.
    |
    */
    'plans' => [
        'pro-1-month' => [
            'key' => 'pro-1-month',
            'name' => 'Pro',
            'description' => 'This is a pro plan',
            'price' => 10000,
            'currency' => 'BDT',
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'featured' => true,
        ],
        'pro-3-month' => [
            'key' => 'pro-3-month',
            'name' => 'Pro',
            'description' => 'This is a pro plan',
            'price' => 20000,
            'currency' => 'BDT',
            'invoice_period' => 3,
            'invoice_interval' => 'month',
            'featured' => false,
        ],
        'pro-6-month' => [
            'key' => 'pro-6-month',
            'name' => 'Pro',
            'description' => 'This is a pro plan',
            'price' => 35000,
            'currency' => 'BDT',
            'invoice_period' => 6,
            'invoice_interval' => 'month',
            'featured' => true,
        ],
        'pro-1-year' => [
            'key' => 'pro-1-year',
            'name' => 'Pro',
            'description' => 'This is a pro plan',
            'price' => 50000,
            'currency' => 'BDT',
            'invoice_period' => 1,
            'invoice_interval' => 'year',
            'featured' => false,
        ],
    ],
];
