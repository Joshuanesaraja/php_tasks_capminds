<?php

$env = parse_ini_file(__DIR__ . "/../.env");

$jwtSecret = $env["JWT_SECRET"];

$accessTokenExpiry = 15 * 60;          // 15 minutes
$refreshTokenExpiry = 2 * 24 * 60 * 60; // 2 days