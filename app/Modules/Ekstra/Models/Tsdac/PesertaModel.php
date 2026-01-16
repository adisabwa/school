<?php

namespace Modules\Ekstra\Models\Tsdac;

use App\Models\BaseModel;

class PesertaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_ts_tsdac_peserta';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->kelas - $d->nama"; });
    }
}