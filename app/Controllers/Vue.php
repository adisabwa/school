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
        return view('index-vue');
    }
}
