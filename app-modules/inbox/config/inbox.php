<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meta Graph API & Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'app_id' => env('FACEBOOK_CLIENT_ID', env('META_APP_ID')),
    'app_secret' => env('FACEBOOK_CLIENT_SECRET', env('META_APP_SECRET')),
    'verify_token' => env('META_WEBHOOK_VERIFY_TOKEN', 'replynat_meta_webhook_secret'),
    'graph_version' => env('META_GRAPH_VERSION', 'v20.0'),
];
