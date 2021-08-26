<?php

/*
 * Copyright (c) 2021 Florian Mathevon <mathevon.florian@gmail.com>
 * JwtManager enables to generate and verify JWT (Json Web Token)
 */

namespace FloWebDev;

class JwtManager
{
    /**
     * @var string The JWT header
     */
    private $header;

    /**
     * @var string The JWT payload
     */
    private $payload;

    /**
     * @var string The JWT signature
     */
    private $signature;

    /**
     * @var string The secret key
     */
    private $key;

    /**
     * JwtManager Constructor
     *
     * @param string $key - Secret key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Generates the JWT
     *
     * @param array $payload - Statements about an entity (typically, the user) and additional data
     * @param int|null $validity - The validity period of the token in seconds, if null it will be unlimited
     * @return string The JWT
     */
    public function getJwt(array $payload, int | null $validity = null): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS512'
        ];

        $payload['exp'] = !empty($validity) ? (time() + $validity) : null;

        $jsonHeader  = json_encode($header);
        $jsonPayload = json_encode($payload);

        $this->header    = $this->base64UrlEncode($jsonHeader);
        $this->payload   = $this->base64UrlEncode($jsonPayload);
        $this->signature = $this->generateSignature($this->header, $this->payload, $this->key);

        return $this->header . '.' . $this->payload . '.' . $this->signature;
    }

    /**
     * Used to check the integrity and validity of a JWT
     *
     * @param string $jwt - JWT
     * @return bool Returns true when the JWT and validity is OK, otherwise false
     */
    public function checkJWT(string $jwt): bool
    {
        $explodedJwt = explode('.', $jwt);

        if (count($explodedJwt) === 3) {
            $this->header    = $explodedJwt[0];
            $this->payload   = $explodedJwt[1];
            $this->signature = $explodedJwt[2];

            $comparativeSignature = $this->generateSignature();

            if ($comparativeSignature === $this->signature) {
                $payload = json_decode($this->base64UrlDecode($this->payload), 1);
                if (!empty($payload['exp'])) {
                    if (intval($payload['exp']) > time()) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generates the JWT signature
     *
     * @return string The JWT signature
     */
    private function generateSignature(): string
    {
        $signature = $this->base64UrlEncode((hash_hmac('sha512', $this->header . "." . $this->payload, $this->key, true)));

        return $signature;
    }

    /**
     * Encodes a string to base64 MIME string
     *
     * @link https://www.php.net/manual/fr/function.base64-encode.php#123098
     * @param string $str - The string to encode
     * @return string The encoded string
     */
    private function base64UrlEncode(string $str): string
    {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($str));
    }

    /**
     * Decodes a base64 MIME string
     *
     * @link https://www.php.net/manual/fr/function.base64-encode.php#123098
     * @param string $str - The string to decode
     * @return string The decoded string
     */
    private function base64UrlDecode($string): string
    {
        return base64_decode(str_replace(['-','_'], ['+','/'], $string));
    }
}
