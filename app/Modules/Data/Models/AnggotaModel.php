<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table         = 'mu_anggota';
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

    public function login($email = '', $no_hp = '', $password = '')
    {
        $data = $this->db->table('mu_anggota i')
                      ->select("i.*, i.id id_anggota, uk.unit_kerja, uk.bidang, ga.id_group, IF(ga.id IS NULL,'0','1') is_mentor")
                    ->join("mu_group_anggota ga","ga.id_anggota=i.id AND ga.type='mentor'","left")
                    ->join("mu__unit_kerja uk","uk.id=i.id_unit","left")
                    ->groupStart()
                      ->where('i.no_hp', $no_hp)
                      ->orWhere('i.email', $email)
                    ->groupEnd()
                    ->where('i.password', $password)
                    ->groupBy('i.id')
                    ->get()
                    ->getRow();
        
        if (!empty($data)) {
          if ($data->role == 'super-admin')
            $allowed_roles = ['super-admin','admin-bidang','admin'];
          else if ($data->role == 'admin-bidang')
            $allowed_roles = ['admin-bidang'];
          else if ($data->role == 'admin')
            $allowed_roles = ['admin'];
          if ($data->is_mentor == '1')
            $allowed_roles[] = 'mentor';
          $allowed_roles[] = 'user';

          $data->allowed_roles = $allowed_roles;
        }

        $data->role = 'user';
      // var_dump($this->db->getLastQuery(), $data);        
        return $data;
    }
    
    public function getAll($whereAnd = [], $whereOr = [], $order = '')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('mu_anggota i')
                    ->select("i.*, i.id id_anggota, uk.unit_kerja, uk.bidang, ga.id_group, IF(ga.id IS NULL,'0','1') is_mentor")
                    ->join("mu_group_anggota ga","ga.id_anggota=i.id AND ga.type='mentor'","left")
                    ->join("mu__unit_kerja uk","uk.id=i.id_unit","left")
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
      $data = $this->db->table('mu_anggota p')
                    ->select('*')
                    ->where($where)
                    ->orderBy('nama')
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
    
    
    public function getData($id)
    {
     
      $data = $this->db->table('mu_anggota i')
      ->select("i.*, i.id id_anggota, uk.unit_kerja, uk.bidang, ga.id_group, IF(ga.id IS NULL,'0','1') is_mentor")
                  ->join("mu_group_anggota ga","ga.id_anggota=i.id AND ga.type='mentor'","left")
                    ->join("mu__unit_kerja uk","uk.id=i.id_unit","left")
                  ->where("i.id", $id)
                  ->groupBy('i.id')
                  ->get()
                  ->getRow();
                  
      return $data;
    }
}