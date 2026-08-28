<?php

class JWT
{
    // This converts data into the Base64URL format used by JWT.
    private static function base64UrlEncode($data)
    {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    // decoding to verify jwt
    private static function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(
            strtr($data, '-_', '+/')
        );
    }

    // Create the JWT
    public static function generate($payload, $secret)
    {
        // Header 
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);

        // encode header into base64 format
        $base64UrlHeader = self::base64UrlEncode($header);

        
        // then encode payload into base64 format
        $base64UrlPayload = self::base64UrlEncode(
            json_encode($payload)
            // first convert payload into json object
        );

        // Signature
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . '.' . $base64UrlPayload,
            $secret,
            true
        );

        $base64UrlSignature = self::base64UrlEncode(
            $signature
        );

        // Final token also used as bearer token
        return $base64UrlHeader . '.' .
               $base64UrlPayload . '.' .
               $base64UrlSignature;
    }

    // verify jwt
    public static function verify($jwt, $secret)
    {
        // 1. Split token 
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signatureProvided = $parts[2];

        // 2. Decode header & payload 
        $decodedPayload = json_decode(
            self::base64UrlDecode($payload),
            true
        );

        if (!$decodedPayload) {
            return false;
        }

        // 3. Recalculate signature 
        $signature = hash_hmac(
            'sha256',
            $header . '.' . $payload,
            $secret,
            true
        );

        $base64UrlSignature = self::base64UrlEncode(
            $signature
        );

        // 4. Verify Signature
        if (!hash_equals(
            $base64UrlSignature,
            $signatureProvided
        )) {
            return false;
            // Signature mismatch (token tampered)
        }

        // 5. Verify token expiry 
        if (
            isset($decodedPayload['exp']) &&
            time() >= $decodedPayload['exp']
        ) {
            return false;
        }

        return $decodedPayload;
    }
}