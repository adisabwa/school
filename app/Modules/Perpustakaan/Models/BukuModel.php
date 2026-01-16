<?php

namespace Modules\Perpustakaan\Models;

use App\Models\BaseModel;

class BukuModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_lib_buku';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->judul; });
    }
}