<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataPenghasilanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_penghasilan';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->label ( Rp. ".number_format($d->dari, 2, ',', '.')." - Rp. ".number_format($d->hingga, 2, ',', '.')." )"; });
    }
}