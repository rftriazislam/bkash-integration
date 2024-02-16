<?php

return [

    //send box 
    "sandbox"             => env("BKASH_SANDBOX", true), //true send box false live
    "bkash_app_key"       => env("BKASH_APP_KEY", "BKASH_APP_KEY"),
    "bkash_app_secret"    => env("BKASH_APP_SECRET", "BKASH_APP_SECRET"),
    "bkash_username"      => env("BKASH_USERNAME", "BKASH_USERNAME"),
    "bkash_password"      => env("BKASH_PASSWORD", "BKASH_PASSWORD"),
    "callbackURL"         => env("BKASH_CALLBACK_URL",  url('/v1/bkash-callback')),
    //send box 


    'timezone'        => 'Asia/Dhaka',
];
