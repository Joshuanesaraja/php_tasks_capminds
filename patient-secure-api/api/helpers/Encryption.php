<?php

function getEncryptionKey()
{
    $env = parse_ini_file(__DIR__ . "/../../.env");

    if (!isset($env["ENCRYPTION_KEY"])) {
        throw new Exception("Encryption key not configured");
    }

    // generated key looked like a 64-character hexadecimal string because:
    // bin2hex(random_bytes(32))

    // Now we convert those 64 characters back into the original 32 bytes with:
    // hex2bin()

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
    // generates a new random IV for every encryption with 16 bytes

    // This is where encryption happens
    $encrypted = openssl_encrypt(
        $data,
        $cipher,
        $key,
        0,
        $iv

        // 0 = default OpenSSL options
    );

    return base64_encode($iv . $encrypted);
    // Because when we later decrypt the data, we need the same IV.
}

function decryptData($encryptedData)
{
    $cipher = "AES-256-CBC";
    $key = getEncryptionKey();

    $data = base64_decode($encryptedData);

    // Extract the IV
    $ivLength = openssl_cipher_iv_length($cipher);

    $iv = substr($data, 0, $ivLength);
    // takes the first 16 bytes.

    $encrypted = substr($data, $ivLength);
    // This takes everything after the first 16 bytes.

    return openssl_decrypt(
        $encrypted,
        $cipher,
        $key,
        0,
        $iv
    );
}