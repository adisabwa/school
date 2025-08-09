<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPembagianModel extends BaseModel
{

    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_pembagian_mapel';
    }

    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table." pm")
                    ->select("k.*, g.*, m.*, pm.*")
                    ->join("sch__guru g","g.id=pm.id_guru")
                    ->join("sch_aka_mapel m","m.id=pm.id_mapel")
                    ->join("sch__kelas k","k.id=pm.id_kelas")
                    ->where($where)
                    ->get()
                    ->getResult();
                    
        foreach ($data as $key => $d) {
            $option = (object)[
                'value' => "$d->id",
                'label' => $d->nama_mapel,
            ];
            if (empty($options[$d->id_kelas])) {
                $options[$d->id_kelas] = (object) [
                    'value' => $d->id_kelas,
                    'label' => $d->kelas,
                    'options' => []
                ];
            } 
            if (empty($options[$d->id_kelas]->options[$d->id_guru])) {
                $options[$d->id_kelas]->options[$d->id_guru] = (object) [
                    'value' => $d->id_guru,
                    'label' => $d->nama,
                    'options' => []
                ];
            } 
            $options[$d->id_kelas]->options[$d->id_guru]->options[$d->id] = $option;
      }
      return $options;
    }
}