<?php

namespace Modules\Keuangan\Models\Data;

use App\Models\BaseModel;

class PosModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_pos';
        $this->relations = [];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, 
        function($d) { return $d->nama_pos; },
            function($opt, $d) {
                $opt->jenis = $d->jenis;
                return $opt;
            }   
        );
    }
}