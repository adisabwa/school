<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSantriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__santri';
        $this->relations = [
          'id_kelas' => [
            'foreign_key' => 'id_kelas',
            'model' => 'DataKelasModel',
            'type' => 'left',
            'selects' => ['kelas'],
          ],
          'id_daerah' => [
            'foreign_key' => 'id_daerah',
            'model' => 'DataDaerahModel',
            'type' => 'left',
            'selects' => ['daerah','daerah_arab'],
          ],
          // 'id_kamar' => [
          //   'foreign_key' => 'id',
          //   'local_key' => 'id_santri',
          //   'model' => 'DataSantriKamarModel',
          //   'type' => 'left',
          //   'selects' => ['rayon','nomor','nama_wamar','nama_wamar_lengkap', "kamar" ],
          // ]
        ];
    }

    public function getOptions($where = ['status' => '0'])
    {
      $options = [];
      $data = $this->db->table('sch__santri p')
                    ->select('p.*, k.kelas')
                    ->join('sch__kelas k','k.id=p.id_kelas','left')
                    ->where($where)
                    ->orderBy('kelas, nama')
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->kelas - $d->nama"
        ];
      }
      return $options;
    }

    public function getKelas()
    {
      $options = [];
      $data = $this->db->table('sch__santri p')
                    ->select('p.kelas')
                    ->groupBy('kelas')
                    ->orderBy('kelas')
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->kelas",
          'label' => "Kelas $d->kelas"
        ];
      }
      return $options;
    }
    
}