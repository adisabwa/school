<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_mapel';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_mapel; });
    }
}