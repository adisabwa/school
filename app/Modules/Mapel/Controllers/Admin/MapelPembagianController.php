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

}