<?php

namespace App\Service;

class PhoneUrlResolver
{
    private $appPublicUrl;

    public function __construct(string $appPublicUrl = '')
    {
        $this->appPublicUrl = rtrim($appPublicUrl, '/');
    }

    public function forPhone(string $absoluteUrl): string
    {
        if ('' !== $this->appPublicUrl) {
            $parts = parse_url($absoluteUrl);
            $path = ($parts['path'] ?? '').(isset($parts['query']) ? '?'.$parts['query'] : '');

            return $this->appPublicUrl.$path;
        }

        $host = parse_url($absoluteUrl, PHP_URL_HOST);
        if (!in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return $absoluteUrl;
        }

        $lan = $this->detectLanIp();
        if (null === $lan) {
            return $absoluteUrl;
        }

        return preg_replace('#^(https?://)(localhost|127\.0\.0\.1|\[::1\])#', '${1}'.$lan, $absoluteUrl, 1);
    }

    private function detectLanIp(): ?string
    {
        $output = shell_exec('ipconfig');
        if (!is_string($output) || '' === $output) {
            return null;
        }

        if (!preg_match_all('/IPv4[^:\r\n]*:\s*([0-9.]+)/', $output, $matches)) {
            return null;
        }

        foreach ($matches[1] as $ip) {
            if (0 === strpos($ip, '192.168.')) {
                return $ip;
            }
        }

        foreach ($matches[1] as $ip) {
            if (0 === strpos($ip, '10.')) {
                return $ip;
            }
        }

        return null;
    }
}
