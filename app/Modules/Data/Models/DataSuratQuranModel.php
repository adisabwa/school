<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class DataSuratQuranModel extends Model
{
    protected $table         = 'mu__surat_quran';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected function initialize()
    {

    }

    public function getTableName()
    {
        return $this->table;
    }
    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table.' p')
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $items = [];
        foreach(range(1, $d->jumlah_ayat) as $i) {
          $items[$i] = (object)[
            'value' => "$d->id-$i",
            'label' => "$i",
          ];
        }
        $options[$d->id] = (object)[
          'value' => "$d->id",
          'label' => "$d->nama_latin",
          'options' => $items,
        ];
      }
      return $options;
    }

    public function countAyat($surat_mulai, $ayat_mulai, $surat_selesai, $ayat_selesai)
    {
      $total_ayat = $this->db->table($this->table.' p')
                    ->select('*, SUM(jumlah_ayat) total_ayat')
                    ->where('id >=', $surat_mulai)
                    ->where('id <', $surat_selesai)
                    ->get()
                    ->getRow();

      $total_ayat = $total_ayat->total_ayat ?? 0;
      $count = $total_ayat - $ayat_mulai + $ayat_selesai + 1;

      return $count;
    }
}