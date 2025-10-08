<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $app_roles = '')
    {
        #Format [app.role1,role2, app.role1,role2, ....]
        $session = session();

        $userdata = $session->get(AUTH_SESS_NAME);
        // return TRUE;

        if (empty($userdata)) {
            return redirect()->to(site_url('auth/unauthorized'));
        } if (array_key_exists('all', $userdata->app_roles) || empty($app_roles)) {
            
        } else {
            $pass = FALSE;
            foreach ($app_roles as $key => $_app_role) {
                [$app, $role] = explode('.', $_app_role);
                if ($userdata->app_roles[$app] == $role || $role == 'all') {
                    $pass = TRUE;
                }
            }
            if (!$pass) {
                return redirect()->to(site_url('auth/forbidden'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}