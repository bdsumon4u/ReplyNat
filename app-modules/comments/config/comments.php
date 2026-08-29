<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meta Graph API Credentials for Comment Automation
    |--------------------------------------------------------------------------
    |
    | Used for 1-Click OAuth connection of Facebook Pages and Instagram accounts,
    | webhook subscription verification, and publishing automated comment replies.
    |
    */
    'app_id' => env('META_APP_ID'),

    'app_secret' => env('META_APP_SECRET'),

    'graph_version' => env('META_GRAPH_VERSION', 'v20.0'),

    'webhook_verify_token' => env('META_WEBHOOK_VERIFY_TOKEN', 'replynat_comments_verify_token'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes for Facebook & Instagram Asset Management
    |--------------------------------------------------------------------------
    */
    'scopes' => [
        'pages_show_list',
        'pages_read_engagement',
        'pages_manage_posts',
        'pages_read_user_content',
        'pages_messaging',
        'pages_manage_metadata',
        'instagram_basic',
        'instagram_manage_comments',
        'instagram_manage_messages',
        'business_management',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Timeouts
    |--------------------------------------------------------------------------
    */
    'timeout' => env('META_API_TIMEOUT', 15),
    'connect_timeout' => env('META_API_CONNECT_TIMEOUT', 5),
];
