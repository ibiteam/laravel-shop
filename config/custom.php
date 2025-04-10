<?php

return [
    'sms_templates' => [
        'phone_code' => env('SMS_PHONE_CODE', ''),
    ],
    'sms_access_key' => env('SMS_ACCESS_KEY', ''),
    'sms_access_secret' => env('SMS_ACCESS_SECRET', ''),
    'sms_sign_name' => env('SMS_SIGN_NAME', ''),
    'kuaidi100' => [
        'host' => env('KUAIDI100_HOST', ''),
        'customer' => env('KUAIDI100_CUSTOMER', ''),
        'key' => env('KUAIDI100_KEY', ''),
    ],
];
