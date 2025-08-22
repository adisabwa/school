<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class PenggunaAksesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__pengguna_akses';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->id_akses $d->role"; });
    }
}