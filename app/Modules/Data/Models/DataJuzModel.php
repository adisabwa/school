<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class DataJuzModel extends Model
{
    protected $table      = 'mu__juz_quran';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

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
    
    
    public function getAll($whereAnd = [], $whereOr = [], $order = '')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('mu__juz_quran i')
                    ->select("i.*")
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table($this->table)
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();

      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->id",
          'surat_mulai' => "$d->surat_mulai",
          'ayat_mulai' => "$d->ayat_mulai",
          'surat_selesai' => "$d->surat_selesai",
          'ayat_selesai' => "$d->ayat_selesai",
          'halaman_mulai' => "$d->halaman_mulai",
          'halaman_selesai' => "$d->halaman_selesai",
        ];
      }
      return $options;
    }
}