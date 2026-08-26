<?php

function getEncryptionKey()
{
    $env = parse_ini_file(__DIR__ . "/../../.env");

    if (!isset($env["ENCRYPTION_KEY"])) {
        throw new Exception("Encryption key not configured");
    }

    $key = hex2bin($env["ENCRYPTION_KEY"]);

    if ($key === false || strlen($key) !== 32) {
        throw new Exception("Encryption key must be 32 bytes");
    }

    return $key;
}

function encryptData($data)
{
    $cipher = "AES-256-CBC";

    $key = getEncryptionKey();

    $iv = random_bytes(openssl_cipher_iv_length($cipher));

    $encrypted = openssl_encrypt(
        $data,
        $cipher,
        $key,
        0,
        $iv
    );

    return base64_encode($iv . $encrypted);
}

function decryptData($encryptedData)
{
    $cipher = "AES-256-CBC";

    $key = getEncryptionKey();

    $data = base64_decode($encryptedData);

    $ivLength = openssl_cipher_iv_length($cipher);

    $iv = substr($data, 0, $ivLength);

    $encrypted = substr($data, $ivLength);

    return openssl_decrypt(
        $encrypted,
        $cipher,
        $key,
        0,
        $iv
    );
}