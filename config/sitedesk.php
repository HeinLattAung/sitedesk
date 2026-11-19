<?php

return [
    // Shown in the header, footer and page titles.
    'company' => env('SITEDESK_COMPANY', 'Kinnari Web Works'),
    'tagline' => env('SITEDESK_TAGLINE', 'Fast, secure websites for Bangkok businesses'),
    'phone' => env('SITEDESK_PHONE', '+66 2 000 0000'),
    'address' => env('SITEDESK_ADDRESS', 'Sathorn, Bangkok 10120'),

    // New contact-form inquiries are emailed here (queued).
    'notify_email' => env('SITEDESK_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS')),

    // Admin account created by `php artisan db:seed`. Read through config(), not
    // env(), so it still works after `php artisan config:cache` in production.
    'admin' => [
        'name' => env('ADMIN_NAME', 'Site Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
