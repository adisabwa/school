<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class PenggunaModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__guru';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->prefix $d->nama $d->suffix"; });
    }

    public function login($email = '', $id = NULL)
    {
        if (empty($email)) $email = -1;
        $where = empty($id) ? ['g.email' => $email] : ['g.id' => $id];

        $data = $this->db->table('sch__guru g')
                        // ->select("*")
                          ->select("a.id id_akses, g.id, g.id id_guru, g.nama, g.email, k.id id_kelas, k.kelas, km.id id_kamar, CONCAT(km.rayon,' ',km.nomor) kamar")
                          ->join('sch__pengguna_akses a','a.id_guru=g.id',"LEFT")
                          ->join('sch__list_akses la',"a.id_akses=la.id","LEFT")
                          ->join('sch__kelas k','k.id_walas=g.id','left')
                          ->join('sch__kamar km','km.id_wali_kamar=g.id','left')
                          ->where($where)
                          ->groupBy('g.id')
                          ->get()
                          ->getRow();
        // var_dump($data, $this->db->getLastQuery());

        if (empty($data)) return;
        
        $data->app_roles = [];
        $data->akses = [];
        $where = [
          "a.id_guru = '$data->id'" => NULL,
          "((a.id_guru = '0' OR a.id_guru IS NULL) AND a.role = 'guru')" => NULL,
        ];
        if ($data->id_kelas)
          $where["((a.id_guru = '0' OR a.id_guru IS NULL) AND a.role = 'walas')" ] = NULL;
        if ($data->id_kamar)
          $where["((a.id_guru = '0' OR a.id_guru IS NULL) AND a.role = 'wamar')" ] = NULL;

        $akses = $this->db->table('sch__pengguna_akses a')
                ->select("la.*, a.*")
                ->join('sch__list_akses la','a.id_akses=la.id')
                ->orWhere($where)
                ->get()
                ->getResult();
        
        // var_dump($this->db->getLastQuery());
        foreach ($akses as $key => $value) {
          $data->app_roles[$value->app] = $value->role;
          $data->akses[$value->app][] = $value;
        }
        return $data;
    }
  
    public function getAll(array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], string $order = '', int $limit = 0, int $offset = 0, $relations = [])
    {
      $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;

      $data = $this->db->table('sch__guru p')
                    ->select("p.*, GROUP_CONCAT(la.nama_app,' ( ',a.role,' ) ' SEPARATOR ',    ') app")
                    ->join('sch__pengguna_akses a','a.id_guru=p.id')
                    ->join('sch__list_akses la','a.id_akses=la.id')
                    ->where($whereAnd)
                    ->groupBy('p.id')
                    ->orderBy($order)
                    ->get()
                    ->getResult();

      return $data;
    }

    
    public function getData($id)
    {
      $data = $this->db->table('sch__guru p')
                    ->select("p.*, GROUP_CONCAT(a.id_akses) as id_akses")
                    ->join('sch__pengguna_akses a','a.id_guru=p.id','left')
                    ->join('sch__list_akses la','a.id_akses=la.id','left')
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