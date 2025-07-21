<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class DataPenghasilanModel extends Model
{
    protected $table         = 'sch__penghasilan';
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
      $data = $this->db->table('sch__penghasilan p')
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->label ( Rp. ".number_format($d->dari, 2, ',', '.')." - Rp. ".number_format($d->hingga, 2, ',', '.')." )"
        ];
      }
      return $options;
    }
}