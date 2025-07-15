<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function __construct()
    {
        
        $this->penggunaModel = model('PenggunaModel');
        // helper('auth');
    }

    public function login($username = NULL, $password = null)
    {
        $username = $username ?? $this->request->getPost('username');
        $password = $password ?? $this->request->getPost('password');

        $user = $this->penggunaModel->login($username, md5($password));
        
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
