<?php

namespace App\Libraries\JWT;

use Config\JWT;

class JWTHandler
{
    protected JWT $config;

    public function __construct()
    {
        $this->config = config('JWT');
    }

    /**
     * Encode data into JWT token
     */
    public function encode(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $this->config->algorithm,
        ];

        // Add standard claims
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->config->expireTime;
        $payload['iss'] = $this->config->issuer;

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = $this->sign($headerEncoded . '.' . $payloadEncoded);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Decode JWT token
     */
    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verify signature
        $signature = $this->base64UrlDecode($signatureEncoded);
        $expectedSignature = $this->sign($headerEncoded . '.' . $payloadEncoded);

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        if (!$payload) {
            return null;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Generate refresh token
     */
    public function generateRefreshToken(array $payload): string
    {
        $payload['exp'] = time() + $this->config->refreshExpireTime;
        $payload['type'] = 'refresh';

        return $this->encode($payload);
    }

    /**
     * Validate refresh token
     */
    public function validateRefreshToken(string $token): ?array
    {
        $payload = $this->decode($token);

        if (!$payload || !isset($payload['type']) || $payload['type'] !== 'refresh') {
            return null;
        }

        return $payload;
    }

    /**
     * Create signature
     */
    protected function sign(string $data): string
    {
        $algorithm = $this->config->supportedAlgorithms[$this->config->algorithm] ?? 'sha256';
        return hash_hmac($algorithm, $data, $this->config->secretKey, true);
    }

    /**
     * Base64 URL encode
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Extract user ID from token
     */
    public function getUserId(string $token): ?int
    {
        $payload = $this->decode($token);
        return $payload['sub'] ?? null;
    }
}
