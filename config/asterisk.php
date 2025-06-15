<?php

return [
    'ami' => [
        'host' => env('ASTERISK_AMI_HOST'),
        'port' => env('ASTERISK_AMI_PORT', 5038),
        'username' => env('ASTERISK_AMI_USERNAME'),
        'secret' => env('ASTERISK_AMI_SECRET'),
        'connect_timeout' => env('ASTERISK_CONNECT_TIMEOUT', 10000), // Añadido (en milisegundos)
        'read_timeout' => env('ASTERISK_READ_TIMEOUT', 10000), 
    ],
];