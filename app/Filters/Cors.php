<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $origin  = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allow   = getenv('CORS_ORIGINS') ?: '';
        $rules   = array_map('trim', explode(',', $allow));

        $ok = false;
        foreach ($rules as $r) {
            if ($r === '*') { $ok = true; break; }
            if ($r && $origin === $r) { $ok = true; break; }
            if (str_starts_with($r, 'https://*.') && $origin) {
                // contoh: https://*.vercel.app
                $suffix = substr($r, 10); // buang "https://*."
                $host   = parse_url($origin, PHP_URL_HOST) ?: '';
                if ($host && str_ends_with($host, $suffix)) { $ok = true; break; }
            }
        }

        if ($origin && $ok) {
            header("Access-Control-Allow-Origin: $origin");
            header('Vary: Origin');
            header('Access-Control-Allow-Credentials: true'); // jika pakai cookie/session
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204); exit; // preflight sukses
        }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
