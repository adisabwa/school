<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $roles = null)
    {
        $session = session();

        $userdata = $session->get(AUTH_SESS_NAME);

        if (empty($userdata)) {
            return redirect()->to(site_url('auth/unauthorized'));
        } else if (!empty($roles) && !in_array($userdata->role, $roles)) {
            return redirect()->to(site_url('auth/forbidden'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}