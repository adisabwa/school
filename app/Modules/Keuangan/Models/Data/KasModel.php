<?php

namespace Modules\Keuangan\Models\Data;

use App\Models\BaseModel;

class KasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_kas';
        $this->relations = [];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_kas; });
    }
}