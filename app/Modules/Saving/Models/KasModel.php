<?php

namespace Modules\Saving\Models;

use App\Models\BaseModel;

class KasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'sav_kas';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama; });
    }
}