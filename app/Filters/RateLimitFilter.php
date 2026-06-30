<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    /**
     * Maximum requests per minute
     */
    protected int $maxRequests = 60;

    /**
     * Time window in seconds
     */
    protected int $timeWindow = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        $cache = service('cache');
        $ip = $request->getIPAddress();
        $key = 'rate_limit_' . md5($ip);

        $data = $cache->get($key);

        if ($data === null) {
            $data = [
                'count' => 1,
                'start' => time(),
            ];
        } else {
            // Check if time window has passed
            if (time() - $data['start'] >= $this->timeWindow) {
                $data = [
                    'count' => 1,
                    'start' => time(),
                ];
            } else {
                $data['count']++;
            }
        }

        // Check if limit exceeded
        if ($data['count'] > $this->maxRequests) {
            $retryAfter = $this->timeWindow - (time() - $data['start']);

            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Limite de requisicoes excedido. Tente novamente em ' . $retryAfter . ' segundos.',
                ])
                ->setHeader('Retry-After', (string) $retryAfter)
                ->setHeader('X-RateLimit-Limit', (string) $this->maxRequests)
                ->setHeader('X-RateLimit-Remaining', '0')
                ->setStatusCode(429);
        }

        // Save to cache
        $cache->save($key, $data, $this->timeWindow);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add rate limit headers to response
        $cache = service('cache');
        $ip = $request->getIPAddress();
        $key = 'rate_limit_' . md5($ip);
        $data = $cache->get($key);

        if ($data !== null) {
            $remaining = max(0, $this->maxRequests - $data['count']);
            $response->setHeader('X-RateLimit-Limit', (string) $this->maxRequests);
            $response->setHeader('X-RateLimit-Remaining', (string) $remaining);
        }

        return $response;
    }
}
