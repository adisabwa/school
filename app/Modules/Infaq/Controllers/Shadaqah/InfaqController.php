<?php

namespace Modules\Infaq\Controllers\Shadaqah;

use App\Controllers\BasePageController;

class InfaqController extends BasePageController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('InfaqShadaqahModel');
    }
    
    public function get_last()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        return $this->respondCreated($this->model->get_last($id_anggota));
    }
    
    public function dashboard()
    {
        return $this->createChart();
    }
    
    public function dashboard_count()
    {
        return $this->createChart('count');
    }

    public function get_before()
    {
        return parent::get_before();
    }
}