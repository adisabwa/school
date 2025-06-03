<?php

namespace Modules\Sholat\Controllers\Wajib;

use App\Controllers\BasePageController;

class SholatController extends BasePageController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = model('SholatWajibModel');
    }

    public function get_last_and_best()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $last = $this->model->get_last($id_anggota);
        $best = $this->model->get_best($id_anggota);

        return $this->respondCreated(compact('last','best'));

    }
    
    public function store()
    {
        // Keep the original behavior
        $data = parent::store();
        // // Add your own logic
        $id = $this->request->getPost('id');
        $this->model->update_total_score($id);
        return $data;

    }
    
    public function dashboard()
    {
        return $this->createChart();
    }

    public function get_before()
    {
        return parent::get_before();
    }
}