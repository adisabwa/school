<?php

namespace Modules\Pengasuhan\Models;

use App\Models\BaseModel;

class NilaiPengasuhanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_peng_nilai';
        $this->relations = [
          'id_santri' => [
            'foreign_key' => 'id_santri',
            'table' => 'sch__santri',
            'selects' => ['nama', 'nisn','id_kelas'],
          ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}