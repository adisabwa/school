<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPembagianModel extends BaseModel
{

    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_pembagian_mapel';
        $this->relations = [
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'table' => PREFIX_TABLE.'_semester',
                'alias' => 'sem',
                'selects' => [
                    'semester',
                    "tahun_ajaran",
                    "minggu",
                    "CONCAT('Semester ', {f}.semester, ' ',{f}.tahun_ajaran) semester_keterangan",
                ]
            ],
            'id_guru' => [
                'foreign_key' => 'id_guru',
                'table' => PREFIX_TABLE.'_guru',
                'selects' => [
                    "nama nama_guru",
                    "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_guru_lengkap",
                    "nama_arab nama_guru_arab",
                    'no_hp no_hp_guru',
                    'nbm nbm_guru',
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
                    'nama_walas',
                    'nama_unit',
                    'nama_walas_lengkap',
                    'nama_walas_arab',
                    'nama_kepala','nama_kepala_lengkap','nama_kepala_arab','kepala_signature','nbm_kepala',
                    'tahun_ajaran tahun_ajaran_kelas'
                ],
                'on_condition' => [
                    "tahun_ajaran=sem.tahun_ajaran"
                ]
            ],
            'id_mapel' => [
                'foreign_key' => 'id_mapel',
                'table' => PREFIX_TABLE.'aka_mapel',
                'selects' => [
                    'nama_mapel',
                    'nama_mapel_arab',
                    'is_kejuruan',
                    'is_praktik',
                ]
            ]
        ];
    }

    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table." pm")
                    ->select("k.*, g.*, m.*, s.*, pm.*")
                    ->join(PREFIX_TABLE."_guru g","g.id=pm.id_guru")
                    ->join(PREFIX_TABLE."aka_mapel m","m.id=pm.id_mapel")
                    ->join(PREFIX_TABLE."_kelas k","k.id=pm.id_kelas")
                    ->join(PREFIX_TABLE."_semester s","s.id=pm.id_semester")
                    ->where($where)
                    ->orderBy('s.id desc, k.id, m.nama_mapel')
                    ->get()
                    ->getResult();
                    
        foreach ($data as $key => $d) {
            $option = (object)[
                'value' => "$d->id_mapel",
                'label' => "$d->nama_mapel ( $d->nama )",
                'pembagian_mapel' => $d,
            ];
            if (empty($options["$d->id_semester"])) {
                $options["$d->id_semester"] = (object) [
                    'value' => "$d->id_semester",
                    'label' => "Semester ".ucfirst($d->semester)." $d->tahun_ajaran",
                    'options' => []
                ];
            } 
            if (empty($options["$d->id_semester"]->options[$d->id_kelas])) {
                $options["$d->id_semester"]->options[$d->id_kelas] = (object) [
                    'value' => $d->id_kelas,
                    'label' => $d->kelas,
                    'tingkat' => $d->tingkat,
                    'id_jurusan' => $d->id_jurusan,
                    'options' => []
                ];
            } 
            $options["$d->id_semester"]->options[$d->id_kelas]->options[] = $option;
      }

      $options = array_values($options);
      foreach ($options as $key => $option) {
          $options[$key]->options = array_values($option->options);
          foreach ($options[$key]->options as $k => $o) {
              $options[$key]->options[$k]->options = array_values($o->options);
          }
      }
      return $options;
    }

    
    public function getOptionsPenjadwalan($where = [])
    {
        $options = [];
        $data = $this->db->table($this->table." pm")
                    ->select("k.*, g.*, m.*, s.*, pm.*")
                    ->join(PREFIX_TABLE."_guru g","g.id=pm.id_guru")
                    ->join(PREFIX_TABLE."aka_mapel m","m.id=pm.id_mapel")
                    ->join(PREFIX_TABLE."_kelas k","k.id=pm.id_kelas")
                    ->join(PREFIX_TABLE."_semester s","s.id=pm.id_semester")
                    ->where($where)
                    ->orderBy('s.id desc, m.nama_mapel')
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