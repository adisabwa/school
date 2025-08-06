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

    public function login($username = '', $password = '', $email = '')
    {
        if (empty($email)) $email = -1;

        $data = $this->db->table('sch_pengguna p')
                     ->select("p.*, GROUP_CONCAT(la.app,'-',a.role ORDER BY a.id) as app_roles, g.id id_guru, g.nama, g.email")
                     ->join('sch_pengguna_akses a','a.id_pengguna=p.id OR (a.id_pengguna IS NULL)','left')
                     ->join('sch_list_akses la',"a.id_akses=la.id",'left')
                     ->join('sch__guru g','g.id=p.id_guru','left')
                     ->groupStart()
                      ->where('p.username', $username)
                      ->where('p.password', $password)
                     ->groupEnd()
                     ->orWhere('g.email',$email)
                     ->groupBy('p.id')
                     ->get()
                     ->getRow();
        // var_dump($data);
        if (empty($data)) 
          $data = $this->db->table('sch__guru g')
                        // ->select("*")
                        ->select("a.id id_akses, a.id_pengguna id, g.id id_guru, g.nama, g.email,
                            GROUP_CONCAT(la.app,'-',a.role) as app_roles")
                          ->join('sch_pengguna_akses a','a.id_pengguna IS NULL')
                          ->join('sch_list_akses la',"a.id_akses=la.id")
                          ->where('g.email',$email)
                          ->where('g.id IS NOT NULL')
                          ->groupBy('g.id')
                          ->get()
                          ->getRow();

        if (empty($data)) return;
        
        // var_dump($data);
        $app_roles = explode(',', $data->app_roles ?? '');
        $ar = [];
        $akses = [];  
        foreach ($app_roles as $key => $var) {
          [$app, $role] = explode('-', $var);
          $ar[$app] = $role;
        }
        $data->app_roles = $ar;
        $data->akses = $this->db->table('sch_pengguna_akses a')
                ->select("la.*, a.*")
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
      $data = $this->db->table('sch_pengguna p')
                    ->select("p.*, GROUP_CONCAT(a.id_akses) as id_akses")
                    ->join('sch_pengguna_akses a','a.id_pengguna=p.id','left')
                    ->join('sch_list_akses la','a.id_akses=la.id','left')
                    ->where('p.id',$id)
                    ->groupBy('p.id')
                    ->get()
                    ->getRow();
      
      if (empty($data)) return;
      $d = $data->id_akses;
      $d = empty($d) ? [] : explode(',', $d);
      $data->id_akses = $d;
      return $data;
    }
}