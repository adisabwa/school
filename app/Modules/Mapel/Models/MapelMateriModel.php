<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelMateriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_materi';
        $this->relations = [
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'table' => PREFIX_TABLE.'_semester',
                'alias' => 'sem',
                'selects' => [
                    'semester',
                    "tahun_ajaran",
                    "minggu",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                ]
            ],
            'id_guru' => [
                'foreign_key' => 'id_guru',
                'table' => PREFIX_TABLE.'_guru',
                'selects' => [
                    "nama nama_guru",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_lengkap",
                    "nama_arab nama_guru_arab",
                    'no_hp no_hp_guru',
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => PREFIX_TABLE.'aka_mapel',
                'selects' => [
                    'nama_mapel',
                    'nama_mapel_arab',
                    'is_kejuruan',
                    'is_praktik',
                ]
            ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_mapel; });
    }
}