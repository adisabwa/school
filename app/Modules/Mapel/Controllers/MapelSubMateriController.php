<?php

namespace Modules\Mapel\Controllers;

use App\Controllers\BaseDataController;

class MapelSubMateriController extends BaseDataController
{
    

    public function __construct()
    {  
        parent::__construct();
        
        $this->model = model('MapelSubMateriModel');
    }

}