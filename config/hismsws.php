<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Details
    |--------------------------------------------------------------------------
    |
    | Set the chosen authentication method,
    | You could use your login credentials or the generated apiKey from hisms.ws account
    | possible values: api, password, or auto
    | if you choose auto, we will look for the apiKey key first,
    | if not found, we look for the mobile and password
    |
    */
   
    
    // Set yor login credentials to communicate with hisms.ws Api
    'mobile' => env('HISMS_WS_MOBILE'),
    'password' =>  env('HISMS_WS_PASSWORD'),
    
    // Name of Sender must be approved by hisms.ws
    'sender' => env('HISMS_WS_SENDER'),

   

    // TODO
//    'domainName' => '',

    /*
    |--------------------------------------------------------------------------
    | Define options for the Http request. (Guzzle http client options)
    |--------------------------------------------------------------------------
    |
    | You do not need to change any of these settings.
    |
    |
    */
    'guzzle' => [
        'client' => [
            // The Base Uri of the Api. Don't Change this Value.
            'base_uri' => 'https://www.hisms.ws/api.php?send_sms&',
        ],
       // https://www.hisms.ws/api.php?
        // Request Options. http://docs.guzzlephp.org/en/stable/request-options.html
        'request' => [
            'http_errors' => true,
            // For debugging
            'debug' => true,
        ],
    ],

];
