<?php

namespace Modules\Ekstra\Controllers\Tsdac;

use App\Controllers\BaseDataController;

class PesertaController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Ekstra\Models\Tsdac\PesertaModel');
    }
}
