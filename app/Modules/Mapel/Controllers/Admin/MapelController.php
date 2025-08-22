<?php

namespace Modules\Mapel\Controllers\Admin;

use App\Controllers\BaseDataController;

class MapelController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        $this->model = model('MapelModel');
    }

}