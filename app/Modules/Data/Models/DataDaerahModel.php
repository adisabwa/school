<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataDaerahModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_daerah';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->daerah; });
    }
}