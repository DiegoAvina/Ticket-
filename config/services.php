<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft Entra ID (login) — Socialite
    |--------------------------------------------------------------------------
    |
    | Reutiliza la misma app de Entra ID que ya usa config/graph.php para
    | enviar correo (MS_CLIENT_ID/MS_CLIENT_SECRET/MS_TENANT_ID), pero para
    | el flujo delegado de login (Authorization Code) en vez del flujo de
    | credenciales de aplicación. En el portal de Azure hay que agregarle a
    | esa misma app: un Redirect URI (igual a MS_REDIRECT_URI) y el permiso
    | delegado "User.Read".
    |
    */

    'microsoft' => [
        'client_id' => env('MS_CLIENT_ID'),
        'client_secret' => env('MS_CLIENT_SECRET'),
        'redirect' => env('MS_REDIRECT_URI'),
        'tenant' => env('MS_TENANT_ID'),
    ],

];
