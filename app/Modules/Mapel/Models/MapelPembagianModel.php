<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPembagianModel extends BaseModel
{

    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_pembagian_mapel';
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
                    "TRIM(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,''))) nama_guru_lengkap",
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
                    'nama_mapel_arab',
                ]
            ]
        ];
    }

    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table." pm")
                    ->select("k.*, g.*, m.*, s.*, pm.*")
                    ->join("sch__guru g","g.id=pm.id_guru")
                    ->join("sch_aka_mapel m","m.id=pm.id_mapel")
                    ->join("sch__kelas k","k.id=pm.id_kelas")
                    ->join("sch__semester s","s.id=pm.id_semester")
                    ->where($where)
                    ->orderBy('m.nama_mapel')
                    ->get()
                    ->getResult();
                    
        foreach ($data as $key => $d) {
            $option = (object)[
                'value' => "$d->id",
                'label' => "$d->nama_mapel ( $d->nama )",
            ];
            if (empty($options[$d->id_semester])) {
                $options[$d->id_semester] = (object) [
                    'value' => $d->id_semester,
                    'label' => "Semester ".ucfirst($d->semester)." $d->tahun_ajaran",
                    'options' => []
                ];
            } 
            if (empty($options[$d->id_semester]->options[$d->id_kelas])) {
                $options[$d->id_semester]->options[$d->id_kelas] = (object) [
                    'value' => $d->id_kelas,
                    'label' => $d->kelas,
                    'options' => []
                ];
            } 
            $options[$d->id_semester]->options[$d->id_kelas]->options[] = $option;
      }
      return $options;
    }

    
    public function getOptionsPenjadwalan($where = [])
    {
        $options = [];
        $data = $this->db->table($this->table." pm")
                    ->select("k.*, g.*, m.*, s.*, pm.*")
                    ->join("sch__guru g","g.id=pm.id_guru")
                    ->join("sch_aka_mapel m","m.id=pm.id_mapel")
                    ->join("sch__kelas k","k.id=pm.id_kelas")
                    ->join("sch__semester s","s.id=pm.id_semester")
                    ->where($where)
                    ->orderBy('m.nama_mapel')
                    ->get()
                    ->getResult();
        
        $options = [];
        foreach ($data as $key => $d) {
            $option = (object)[
                'value' => "$d->id",
                'label' => "$d->kode_mapel - $d->nama_mapel",
                'match' => "$d->kelas - $d->kode_mapel",
            ];
            if (empty($options[$d->id_kelas])) {
                $options[$d->id_kelas] = (object) [
                    'value' => $d->id_kelas,
                    'label' => "$d->kelas",
                    'options' => []
                ];
            } 
            $options[$d->id_kelas]->options[] = $option;
        }
        return $options;
    }
}