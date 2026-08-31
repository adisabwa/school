<?php

namespace Modules\Presensi\Models;

use App\Models\BaseModel;

class PresensiMengajarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'pre_mengajar_kelas';
        $this->relations = [
            PREFIX_TABLE.'pre_harian' => [
                'table' => "(SELECT id_mengajar_kelas, COUNT(IF(kehadiran='hadir',1,NULL)) hadir,
                    COUNT(IF(kehadiran='izin',1,NULL)) izin,
                    COUNT(IF(kehadiran='alfa',1,NULL)) alfa,
                    COUNT(IF(kehadiran='sakit',1,NULL)) sakit
                FROM ".PREFIX_TABLE."pre_harian GROUP BY id_mengajar_kelas)",
                'selects' => [
                    'hadir','izin','alfa','sakit',
                    '({f}.hadir * jam) as total_hadir',
                    '({f}.izin * jam) as total_izin',
                    '({f}.alfa * jam) as total_alfa',
                    '({f}.sakit * jam) as total_sakit',
                ],
                'local_key' => 'id_mengajar_kelas',
                'foreign_key' => 'id',
                'alias' => 'harian',
                'type' => 'left',
            ],
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'table' => PREFIX_TABLE.'_semester',
                'selects' => [
                    'semester',
                    "tahun_ajaran",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                ]
            ],
            'id_guru' => [
                'foreign_key' => 'id_guru',
                'table' => PREFIX_TABLE.'_guru',
                'selects' => [
                    "nama nama_guru",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_lengkap",
                ]
            ],
            'id_kelas' => [
                'foreign_key' => 'id_kelas',
                'table' => PREFIX_TABLE.'_kelas',
                'selects' => [
                    'kelas',
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => PREFIX_TABLE.'aka_mapel',
                'selects' => [
                    'nama_mapel',
                ]
            ],
            'id_sesi' => [
                'foreign_key' => 'id_sesi',
                'table' => PREFIX_TABLE.'_sesi',
                'type' => 'left',
                'selects' => [
                    'sesi sesi_awal','sesi','waktu_mulai','waktu_selesai',
                ]
            ],
            'sesi_akhir' => [
                'table' => PREFIX_TABLE.'_sesi',
                'type' => 'left',
                'alias' => 'sesi_akhir',
                'on_condition' => [
                    '{n}sesi_akhir.sesi=('.PREFIX_TABLE.'_sesi.sesi + '.PREFIX_TABLE.'pre_mengajar_kelas.jam - 1)'
                ],
                'selects' => [
                    'id id_sesi_akhir','sesi sesi_akhir','waktu_mulai waktu_mulai_akhir','waktu_selesai waktu_selesai_akhir',
                ]
            ],
            'id_pengganti' => [
                'foreign_key' => 'id_pengganti',
                'table' => PREFIX_TABLE.'_guru',
                'alias' => 'gu1',
                'type' => 'left',
                'selects' => [
                    "nama nama_guru_pengganti",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_pengganti_lengkap",
                ]
            ],
            'created_by' => [
                'foreign_key' => 'created_by',
                'table' => PREFIX_TABLE.'_guru',
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