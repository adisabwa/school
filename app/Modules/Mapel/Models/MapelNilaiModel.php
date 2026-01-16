<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelNilaiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_nilai';
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
            'id_kelas' => [
                'foreign_key' => 'id_kelas',
                'model' => 'DataKelasModel',
                'selects' => [
                    'kelas',
                    'tingkat',
                    'nama_walas',
                    'nama_walas_lengkap',
                    'nama_walas_arab',
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => 'sch_aka_mapel',
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
        $table = $model->getTableName();
        $model->selects = [
            "{n}COUNT(sch_aka_pembagian_mapel.id)",
            "{n}COUNT(IF(nilai_harian IS NULL or nilai_harian=0,NULL, 1))", "{n}COUNT(nilai_harian)",
            "{n}COUNT(IF(nilai_harian IS NULL or nilai_harian=0,NULL, 1)) / COUNT(nilai_harian) as persentase_nilai_harian",
            "{n}COUNT(IF(uts IS NULL or uts =0,NULL, 1)) / COUNT(uts ) as persentase_uts ",
            "{n}COUNT(IF(uas IS NULL or uas =0,NULL, 1)) / COUNT(uas ) as persentase_uas"
        ];
        return $model->getAll(whereAnd:[
            "id_semester" => $id_semester,
            "id_kelas" => $id_kelas,
            "id_guru" => $id_guru,
        ], relations:[
            'id_pembagian_mapel' => [
                'foreign_key' => 'id_semester',
                'local_key' => 'id_semester',
                'type' => 'left',
                'table' => 'sch_aka_nilai',
                'condition' => [
                    "id_semester=$table.id_semester",
                    "id_kelas=$table.id_kelas",
                    "id_mapel=$table.id_mapel",
                ],
                'selects' => [],
            ]
        ], 
        groupBy: ['id_semester','id_kelas','id_mapel'],
        order:"tingkat, id_kelas, nama_mapel"
        );

        $datas = $this->getAll(whereAnd:[
            "id_semester" => $id_semester,
            "id_kelas" => $id_kelas,
            "{n}id_guru" => $id_guru,
        ], groupBy: ['id_semester','id_kelas','id_mapel'],
        order:"tingkat, id_kelas, nama_mapel");
        
    }

}