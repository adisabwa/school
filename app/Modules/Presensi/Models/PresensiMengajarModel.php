<?php

namespace Modules\Presensi\Models;

use App\Models\BaseModel;

class PresensiMengajarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_pre_mengajar_kelas';
        $this->relations = [
            'sch_pre_harian' => [
                'table' => "(SELECT id_mengajar_kelas, COUNT(IF(kehadiran='hadir',1,NULL)) hadir,
                    COUNT(IF(kehadiran='izin',1,NULL)) izin,
                    COUNT(IF(kehadiran='alfa',1,NULL)) alfa,
                    COUNT(IF(kehadiran='sakit',1,NULL)) sakit
                FROM sch_pre_harian GROUP BY id_mengajar_kelas)",
                'selects' => ['hadir','izin','alfa','sakit'],
                'local_key' => 'id_mengajar_kelas',
                'foreign_key' => 'id',
                'alias' => 'harian',
                'type' => 'left',
            ],
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