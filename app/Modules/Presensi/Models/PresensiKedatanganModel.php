<?php

namespace Modules\Presensi\Models;

use App\Models\BaseModel;

class PresensiKedatanganModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'pre_kedatangan';
        $this->relations = [
            'id_guru' => [
                'foreign_key' => 'id_guru',
                'model' => 'DataGuruModel',
                'selects' => [
                    'nama',
                    'nama_arab',
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_lengkap",
                    'email',
                    'nbm'],
            ],
        ];
        $this->selects = ['id'];
    }

    public function getOptions($where = [])
    {
        return [];
        return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}