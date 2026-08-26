<?php

$env = parse_ini_file(__DIR__ . '/../.env');

return [
    'db_host' => $env['DB_HOST'],
    'db_port' => (int) $env['DB_PORT'],
    'db_name' => $env['DB_NAME'],
    'db_user' => $env['DB_USER'],
    'db_pass' => $env['DB_PASS'],

    'jwt_secret' => $env['JWT_SECRET'],
    'jwt_expiry' => (int) $env['JWT_EXPIRY']
];