<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataJabatanGuruModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_jabatan_guru';
        $this->relations = [
            'id_jabatan' => [
                'foreign_key'   => 'id_jabatan',
                'model'         => 'DataJabatanModel',
                'selects'       => ['jabatan']
            ],
            'id_guru' => [
                'foreign_key'   => 'id_guru',
                'model'         => 'DataGuruModel',
                'selects'       => ['nama nama_guru','nama_arab nama_guru_arab']
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->nama_guru ($d->jabatan)"; });
    }
}