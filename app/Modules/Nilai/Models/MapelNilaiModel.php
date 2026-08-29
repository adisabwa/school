<?php

namespace Modules\Nilai\Models;

use App\Models\BaseModel;

class MapelNilaiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_nilai';
        $this->relations = [
            'id_rapor' => [
                'foreign_key' => 'id_rapor',
                'model' => 'RaporModel',
                'pass_key' => ['id_nilai'],
                'selects' => [
                    'id_semester','id_santri','id_kelas',
                    'semester',
                    "tahun_ajaran",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                    'kelas',
                    'tingkat',
                    'nama_walas',
                    'nama_walas_lengkap',
                    'nama_walas_arab',
                    'nama',
                    'nama_arab',
                    'status_santri', 'kelas_santri',
                    'catatan', 'korikuler',
                    'ranking_kelas', 'ranking_angkatan','naik_kelas',
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => PREFIX_TABLE.'aka_mapel',
                'selects' => [
                    'nama_mapel',
                    'nama_mapel_arab',
                    'is_kejuruan',
                ]
            ]
        ];
    }

    public function get_progres($id_semester = NULL, $id_kelas = NULL, $id_guru = NULL)
    {
        // var_dump($id_semester, $id_kelas, $id_guru);
        $model = model('MapelPembagianModel');
        $mapels = $model->getAll(whereAnd:[
            "id_semester" => $id_semester,
            "id_kelas" => $id_kelas,
            "id_guru" => $id_guru,
        ]);
        // var_dump($mapels);
        $orWhere = array_reduce($mapels, function($carry, $item) {
            $where = [
                "(id_mapel=$item->id_mapel) AND (id_kelas=$item->id_kelas)"
            ];
            return is_array($carry) ? array_merge($carry, $where) : $where;
        }, 0);
        // var_dump($id_mapels);
        $model->selects = [
            "{n}COUNT(".PREFIX_TABLE."aka_pembagian_mapel.id)",
            "{n}COUNT(IF(nilai_harian IS NULL or nilai_harian=0,NULL, 1))", "{n}COUNT(nilai_harian)",
            "{n}COUNT(IF(nilai_harian IS NULL or nilai_harian=0,NULL, 1)) / COUNT(nilai_harian) as persentase_nilai_harian",
            "{n}COUNT(IF(uts IS NULL or uts =0,NULL, 1)) / COUNT(uts ) as persentase_uts ",
            "{n}COUNT(IF(uas IS NULL or uas =0,NULL, 1)) / COUNT(uas ) as persentase_uas"
        ];
        return $this->getAll(whereAnd:[
            "{n}id_semester" => $id_semester,
        ],
        whereOr:$orWhere,
        groupBy: ['id_semester','id_kelas','id_mapel'],
        order:"tingkat, id_kelas, nama_mapel"
        );
        
    }
    

}