<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS', ''),
        'host'           => env('DB_HOST', 'exa01-scan.latam.corp.net'),
        'port'           => env('DB_PORT', '1521'),
        'database'       => env('DB_DATABASE', 'xe'),
        'service_name'   => env('DB_SERVICE_NAME', 'CSR.br1.ocm.s1723313.oraclecloudatcustomer.com'),
        'username'       => env('DB_USERNAME', 'csr_dev'),
        'password'       => env('DB_PASSWORD', 'p423c'),
        'charset'        => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'server_version' => env('DB_SERVER_VERSION', '11g'),
    ],
];
