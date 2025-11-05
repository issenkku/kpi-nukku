<?php

return [
    // Application identifier registered with the SSO provider
    'app_id' => env('SSO_APP_ID', ''),

    // OAuth client credentials
    'client_id' => env('SSO_CLIENT_ID', ''),
    'client_secret' => env('SSO_CLIENT_SECRET', ''),

    // Callback/redirect URL configured at the SSO provider
    'redirect_url' => env('SSO_REDIRECT_URL', ''),
];

