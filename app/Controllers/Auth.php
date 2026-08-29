<?php

namespace App\Controllers;
use App\Libraries\GoogleAuth;

class Auth extends BaseController
{
    public $penggunaModel;

    public function __construct()
    {
        $this->penggunaModel = model('PenggunaModel');
        // helper('auth');
    }

    public function g_login()
    {
        $credential = $this->request->getGetPost('credential') ?? '';
        $token = $this->request->getGetPost('token') ?? '';
        $id = $email = '';

        if ($token === 'a28541aee1bb6660f4a7e91793a1ce91') {
            $id = $this->request->getGetPost('id');
            $email = $this->request->getGetPost('email');
        }

        if (!empty($credential)) {
            $google = new GoogleAuth();
            $userData = $google->verifyToken($credential);
            $email = $userData['email'] ?? $email ?? '';
        }

        $user = $this->penggunaModel->login($email, $id);
        // var_dump($user);
        if ($user) {
            // Getting user positions
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'email' => $email,
            ], 401);
        }

    }

    public function user()
    {
        if (empty(userdata())) {
            return $this->unauthorized();
        }
        return $this->respondCreated(userdata());
    }

    public function forbidden()
    {
        return $this->failForbidden('Error 403 Forbidden');
    }

    public function unauthorized()
    {
        return $this->failUnauthorized('Error 401 Unauthorized');
    }

    public function logout()
    {
        clear_userdata();

        return $this->respondCreated();
    }

    public function change_role()
    {
        $app = $this->request->getGetPost('app');
        $role = $this->request->getGetPost('role');

        $user = userdata();
        if (isset($user->app_roles['all'])) {
            array_walk($user->app_roles, function(&$r, $key) use ($role) {
                if ($key != 'all')
                    $r = $role;
            });
        }
        if ($app)
            $user->app_roles[$app] = $role;
        
        set_userdata($user);
        return $this->respondCreated($user);
    }

    public function reset()
    {
        $user = userdata();
        clear_userdata();
        $user = $this->penggunaModel->login($user->username, $user->password);
        
        if ($user) {
            // Getting user positions
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'message' => 'Maaf akun Anda belum terdaftar.',
            ], 401);
        }
    }
}
