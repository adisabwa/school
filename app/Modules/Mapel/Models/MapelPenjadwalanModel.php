<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPenjadwalanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_penjadwalan';
    }

    public function getOptions($where = [])
    {
        $options = [];
        $data = $this->db->table($this->table." pm")
                    ->select("s.*, pm.*")
                    ->join("sch__semester s","s.id=pm.id_semester","left")
                    ->where($where)
                    ->orderBy('s.id desc')
                    ->get()
                    ->getResult();
         
        foreach ($data as $key => $d) {
            $options[] = (object)[
                'value' => "$d->id",
                'label' => "Semester ".ucfirst($d->semester)." $d->tahun_ajaran versi $d->versi",
            ];
        }
        return $options;
    }
}