<?php

namespace Modules\Rapor\Models;

use App\Models\BaseModel;

class RaporModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_rapor';
        $this->relations = [
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'table' => PREFIX_TABLE.'_semester',
                'alias' => 'sem',
                'selects' => [
                    'semester',
                    "tahun_ajaran",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                ]
            ],
            'id_kelas_ajar' => [
                'foreign_key' => 'id_kelas',
                'local_key' => 'id_kelas',
                'model' => 'DataKelasAjarModel',
                'alias' => 'kelas_ajar',
                'selects' => [
                    'kelas',
                    'tingkat',
                    'id_jurusan',
                    'nama_walas',
                    'nama_walas_lengkap',
                    'nama_walas_arab',
                ],
                'on_condition' => [
                    'tahun_ajaran=sem.tahun_ajaran'
                ]
            ],
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'model' => 'DataSantriModel',
                'selects' => [
                    'nama',
                    'nama_arab',
                    'status status_santri',
                    'id_kelas kelas_santri',
                ]
            ],
            'id_nilai' => [
                'foreign_key' => 'id',
                'local_key' => 'id_rapor',
                'type' => 'left',
                'model' => 'MapelNilaiModel',
                'pass_key' => ['id_rapor','id_mapel'],
                'group_by' => ['id_rapor'],
                'selects' => [
                    'total_nilai_harian','total_uts','total_uas','total_um','total_nilai_rapor',
                    'total_katrol1','total_katrol2',
                ],
                'inner_selects' => [
                    'SUM({f}nilai_harian) total_nilai_harian',
                    'SUM({f}uts) total_uts',
                    'SUM({f}uas) total_uas',
                    'SUM({f}um) total_um',
                    'SUM({f}nilai_rapor) total_nilai_rapor',
                    'SUM({f}katrol1) total_katrol1',
                    'SUM({f}katrol2) total_katrol2',
                ]
            ],
        ];
    }

    public function get_progres($id_semester = NULL, $id_kelas = NULL, $id_guru = NULL)
    {
        // var_dump($id_semester, $id_kelas, $id_guru);
        $model = model('MapelPembagianModel');
        $table = $model->getTableName();
        $model->selects = [
            "{n}COUNT(".PREFIX_TABLE."aka_pembagian_mapel.id)",
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
                'table' => PREFIX_TABLE.'aka_nilai',
                'on_condition' => [
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