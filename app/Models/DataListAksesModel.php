<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataListAksesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_list_akses';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_app; });
    }
}