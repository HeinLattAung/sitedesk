<?php

return [
    // Shown in the header, footer and page titles.
    'company' => env('SITEDESK_COMPANY', 'Sathorn Web Studio'),
    'tagline' => env('SITEDESK_TAGLINE', 'Fast, secure websites for Bangkok businesses'),
    'phone' => env('SITEDESK_PHONE', '+66 2 000 0000'),
    'address' => env('SITEDESK_ADDRESS', 'Sathorn, Bangkok 10120'),

    // New contact-form inquiries are emailed here (queued).
    'notify_email' => env('SITEDESK_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS')),
];
