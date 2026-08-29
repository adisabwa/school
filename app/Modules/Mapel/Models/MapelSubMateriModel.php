<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelSubMateriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_sub_materi';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->sub_materi; });
    }
}