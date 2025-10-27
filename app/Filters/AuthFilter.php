<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $s = session();

        // sesuaikan dengan kunci yang kamu set saat login
        if ($s->get('isLoggedIn') === true && $s->get('user')) {
            return; // sudah login
        }

        // request API → balas 401 JSON
        $isApi  = str_starts_with($request->getPath(), 'api/')
               || stripos($request->getHeaderLine('Accept'), 'application/json') !== false
               || $request->isAJAX();

        if ($isApi) {
            return Services::response()
                ->setJSON(['message' => 'Unauthorized'])
                ->setStatusCode(401);
        }

        // request halaman → redirect ke login
        return redirect()->to(site_url('login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
