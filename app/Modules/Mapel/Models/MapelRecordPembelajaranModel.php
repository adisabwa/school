<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelRecordPembelajaranModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_record_pembelajaran';
        $this->relations = [
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'table' => 'sch__semester',
                'selects' => [
                    'semester',
                    "tahun_ajaran",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                ]
            ],
            'id_guru' => [
                'foreign_key' => 'id_guru',
                'table' => 'sch__guru',
                'selects' => [
                    "nama nama_guru",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_lengkap",
                ]
            ],
            'id_kelas' => [
                'foreign_key' => 'id_kelas',
                'table' => 'sch__kelas',
                'selects' => [
                    'kelas',
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => 'sch_aka_mapel',
                'selects' => [
                    'nama_mapel',
                ]
            ],
            'id_sesi' => [
                'foreign_key' => 'id_sesi',
                'table' => 'sch__sesi',
                'selects' => [
                    'sesi','waktu_mulai','waktu_selesai',
                ]
            ],
            'id_pengganti' => [
                'foreign_key' => 'id_pengganti',
                'table' => 'sch__guru',
                'alias' => 'gu1',
                'type' => 'left',
                'selects' => [
                    "nama nama_guru_pengganti",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_pengganti_lengkap",
                ]
            ],
            'created_by' => [
                'foreign_key' => 'created_by',
                'table' => 'sch__guru',
                'alias' => 'gu2',
                'type' => 'left',
                'selects' => [
                    "nama nama_pembuat",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_pembuat_lengkap",
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
        return [];
        return $this->getOptionsData($where, function($d) { return $d->nama_mapel; });
    }
}