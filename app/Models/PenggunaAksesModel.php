<?php

namespace App\Models;

use App\Models\BaseModel;

class PenggunaAksesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_pengguna_akses';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->id_akses $d->role"; });
    }
}