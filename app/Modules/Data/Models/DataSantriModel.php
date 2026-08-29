<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSantriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_santri';
        $this->relations = [
          // 'id_kelas' => [
          //   'foreign_key' => 'id_kelas',
          //   'model' => 'DataKelasModel',
          //   'type' => 'left',
          //   'selects' => ['kelas','tingkat','id_jurusan','id_unit','nama_unit','nama_jurusan'],
          // ],
          'id_daerah' => [
            'foreign_key' => 'id_daerah',
            'model' => 'DataDaerahModel',
            'type' => 'left',
            'selects' => ['daerah','daerah_arab'],
          ],
          'id_kamar' => [
            'foreign_key' => 'id',
            'local_key' => 'id_santri',
            'model' => 'DataSantriKamarModel',
            'type' => 'left',
            'pass_key' => ['id_santri'],
            'selects' => ['id_kamar', 'rayon','nomor','nama_wamar','nama_wamar_lengkap', 'nama_wamar_arab', "kamar" ],
          ], 
          'id_kelas' => [
            'foreign_key' => 'id',
            'local_key' => 'id_santri',
            'model' => 'DataSantriKelasModel',
            'type' => 'left',
            'pass_key' => ['id_santri'],
            'group_by' => ['id_kelas'],
            'selects' => ['id_kelas', 'kelas','tingkat','id_jurusan','id_unit','nama_unit','nama_jurusan','tahun_ajaran'],
            'order' => ['tahun_ajaran desc'],
          ]
        ];
    }

    public function getOptions($where = ['status' => '0'])
    {
      return $this->getOptionsData(where: $where, 
        order: 'kelas, nama',
        concatFunc : function($d) { 
            
            // var_dump($d);exit;
            return "$d->nama ($d->kelas)"; 
          },
        addOptions : function($option, $data) { 
          $option->status = $data->status ?? null;
          return $option; 
          }
        );
    }

    public function getKelas()
    {
      $options = [];
      $data = $this->db->table(PREFIX_TABLE.'_santri p')
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