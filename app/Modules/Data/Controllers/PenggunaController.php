<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class PenggunaController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PenggunaModel');
        $this->modelAkses = model('PenggunaAksesModel');
    }

    
    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->model->find($id);
        if (!empty($data))
        $data->sch_pengguna_akses = array_map(function($val){
            return (object)[
                'id_pengguna'    => $val->id_pengguna,
                'id_akses'    => $val->id_akses,
                'role'    => $val->role,
            ];
        }, $this->modelAkses->getAll(['id_pengguna' => $id]) );

        return $this->respondCreated(($data));
    }
}
