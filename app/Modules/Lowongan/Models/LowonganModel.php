<?php

namespace Modules\Lowongan\Models;

use App\Models\BaseModel;

class LowonganModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'lowongan';
    }

}