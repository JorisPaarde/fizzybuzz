<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inbound e-mailadres voor prijsupload
    |--------------------------------------------------------------------------
    */

    'inbound_email' => env('INBOUND_EMAIL_ADDRESS', 'upload@pricesignal.nl'),

    'mailgun_webhook_signing_key' => env('MAILGUN_WEBHOOK_SIGNING_KEY'),

];
