<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table         = 'sch_pengguna';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Pengguna::class;
    protected $returnType    = 'object';

    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // protected $db;

    protected function initialize()
    {
        // $this->db = $this->builder();
    }
    
    public function getTableName()
    {
        return $this->table;
    }

    public function login($username = '', $password = '')
    {
        $data = $this->db->table('sch_pengguna p')
                     ->select("p.*, GROUP_CONCAT(la.app) as app")
                     ->join('sch_pengguna_akses a','a.id_pengguna=p.id','left')
                     ->join('sch_list_akses la','a.id_akses=la.id','left')
                     ->where('p.username', $username)
                     ->where('p.password', $password)
                     ->groupBy('p.id')
                     ->get()
                     ->getRow();

        if (empty($data)) return;
        
        $data->app = explode(',', $data->app ?? '');
        $data->akses = $this->db->table('sch_pengguna_akses a')
                ->select("la.*")
                ->join('sch_list_akses la','a.id_akses=la.id')
                ->where(['a.id_pengguna' => $data->id])
                ->get()
                ->getResult();
        
        return $data;
    }
    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->where($where)
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->nama"
        ];
      }
      return $options;
    }

    public function getAll($where = [], $order = '')
    {
      $data = $this->db->table('sch_pengguna p')
                    ->select("p.*, GROUP_CONCAT(la.nama_app SEPARATOR ',    ') app")
                    ->join('sch_pengguna_akses a','a.id_pengguna=p.id','left')
                    ->join('sch_list_akses la','a.id_akses=la.id','left')
                    ->where($where)
                    ->groupBy('p.id')
                    ->orderBy($order)
                    ->get()
                    ->getResult();

      return $data;
    }

    
    public function getData($id)
    {
      $key = 'sch_pengguna_akses-id_akses';
      $data = $this->db->table('sch_pengguna p')
                    ->select("p.*, GROUP_CONCAT(a.id_akses) as '$key'")
                    ->join('sch_pengguna_akses a','a.id_pengguna=p.id','left')
                    ->join('sch_list_akses la','a.id_akses=la.id','left')
                    ->where('p.id',$id)
                    ->groupBy('p.id')
                    ->get()
                    ->getRow();
                    
      $d = $data->{$key};
      $d = empty($d) ? [] : explode(',', $d);
      $data->{$key} = $d;
      return $data;
    }
}