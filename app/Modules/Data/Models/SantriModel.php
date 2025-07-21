<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    protected $table         = 'sch__santri';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Prodi::class;
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

        $data = $this->db->table('sch__santri i')
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

    public function getOptions($where = ['status' => '0'])
    {
      $options = [];
      $data = $this->db->table('sch__santri p')
                    ->select('*')
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