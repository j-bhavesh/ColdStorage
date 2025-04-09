<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'auth_key' => env('SMS_AUTH_KEY'),
        'auth_token' => env('SMS_AUTH_TOKEN'),
        'sender_id' => env('SMS_SENDER_ID'),
        'farmer_registration_template_id' => env('FARMER_REGISTRATION'),
        'seed_booking_template_id' => env('SEED_BOOKING'),
        'seed_distribution_template_id' => env('SEED_DISTRBUTION'),
        'potato_booking_template_id' => env('POTATO_BOOKING'),
        'packaging_distribution_template_id' => env('PACKAGING_DISTRIBUTION'),
        'storage_loading_template_id' => env('STORAGE_LOADING'),
        'advance_payment_template_id' => env('ADVANCE_PAYMENT'),
    ],

];
