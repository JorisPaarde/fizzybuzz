<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inbound e-mailadres voor prijsupload
    |--------------------------------------------------------------------------
    |
    | Leden sturen facturen/prijslijsten vanaf hun geregistreerde e-mailadres
    | naar dit adres. Configureer inbound routing via Mailgun of Postmark.
    |
    */

    'inbound_email' => env('INBOUND_EMAIL_ADDRESS', 'upload@prijsplein.nl'),

    'mailgun_webhook_signing_key' => env('MAILGUN_WEBHOOK_SIGNING_KEY'),

];
