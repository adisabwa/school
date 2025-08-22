<?php

namespace Modules\Mapel\Controllers\Admin;

use App\Controllers\BaseDataController;

class MapelPembagianController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        $this->model = model('MapelPembagianModel');
    }

    public function options_penjadwalan()
    {
        $where = $this->request->getGet('where') ?? [];
        // var_dump($where);
        return $this->respondCreated($this->model->getOptionsPenjadwalan($where));
    }
}