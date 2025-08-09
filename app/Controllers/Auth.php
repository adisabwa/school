<?php

namespace App\Controllers;
use App\Libraries\GoogleAuth;

class Auth extends BaseController
{
    public function __construct()
    {
        
        $this->penggunaModel = model('PenggunaModel');
        // helper('auth');
    }

    public function g_login()
    {
        $credential = $this->request->getPost('credential') ?? '';

        $google = new GoogleAuth();
        $userData = $google->verifyToken($credential);
        $email = $userData['email'] ?? $this->request->getGetPost('email') ?? '';

        $user = $this->penggunaModel->login($email);
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
