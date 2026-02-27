<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport'  => 'smtp',
            'url'        => env('MAIL_URL'),
            'host'       => env('MAIL_HOST', '127.0.0.1'),
            'port'       => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'timeout'    => null,
        ],
        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],
        'array'    => ['transport' => 'array'],
        'failover' => ['transport' => 'failover', 'mailers' => ['smtp', 'log']],
        'sendmail' => ['transport' => 'sendmail', 'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i')],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@taskflow.dev'),
        'name'    => env('MAIL_FROM_NAME', 'TaskFlow Pro'),
    ],

];
