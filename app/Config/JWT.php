<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    /**
     * JWT Secret Key
     */
    public string $secretKey = '';

    /**
     * JWT Algorithm
     */
    public string $algorithm = 'HS256';

    /**
     * Token expiration time in seconds (default: 1 hour)
     */
    public int $expireTime = 3600;

    /**
     * Refresh token expiration time in seconds (default: 7 days)
     */
    public int $refreshExpireTime = 604800;

    /**
     * Token issuer
     */
    public string $issuer = 'gpsimports';

    /**
     * Supported algorithms
     */
    public array $supportedAlgorithms = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha512',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->secretKey = env('jwt.secretKey', 'default_secret_key_change_in_production');
        $this->algorithm = env('jwt.algorithm', 'HS256');
        $this->expireTime = (int) env('jwt.expireTime', 3600);
        $this->refreshExpireTime = (int) env('jwt.refreshExpireTime', 604800);
    }
}
