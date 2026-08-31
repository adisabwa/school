<?php

namespace App\Controllers;

class Vue extends BaseController
{

    public function __construct()
    {
        $this->helpers[] = 'vite';
        helper('vite');
    }
    

    public function index()
    {
        $allSegments = $this->request->uri->getSegments();
        $app = $allSegments[1] ?? 'dashboard';
        if (!in_array($app, ['data','psb','rapor','mapel','kmi','keuangan','ekstra','pengasuhan','perpustakaan','presensi','saving'])) {
            $app = 'dashboard';
        }
        // var_dump($allSegments);exit;
        return view('index-vue', [
            'app' => $app,
        ]);
    }
}
