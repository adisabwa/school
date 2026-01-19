<?php

namespace Modules\Presensi\Models;

use App\Models\BaseModel;

class PresensiSantriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_pre_harian';
        $this->relations = [
            'id_mengajar_kelas' => [
                'foreign_key' => 'id_mengajar_kelas',
                'model' => 'PresensiMengajarModel',
                'alias' => 'mengajar',
                'selects' => ['id_semester','id_kelas','kelas','id_guru','id_mapel','kode_mapel','tanggal','id_sesi','jam']
            ],
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'model' => 'DataSantriModel',
                'selects' => ['nama','nama_arab','nisn','stb','id_daerah','daerah'],
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