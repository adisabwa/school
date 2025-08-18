<?php

namespace Modules\Mapel\Controllers\Admin;

use App\Controllers\BaseDataController;

class MapelPenjadwalanController extends BaseDataController
{
    public $detailModel;

    public function __construct()
    {
        $this->model = model('MapelPenjadwalanModel');
    }

}