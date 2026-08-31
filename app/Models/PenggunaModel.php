<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_guru';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->prefix $d->nama $d->suffix"; });
    }

    public function login($email = '', $id = NULL)
    {
        if (empty($email)) $email = -1;
        $where = empty($id) ? ['g.email' => $email] : ['g.id' => $id];

        $data = $this->db->table(PREFIX_TABLE.'_guru g')
                        // ->select("*")
                          ->select("a.id id_akses, g.id, g.id id_guru, g.nama, g.email, k.id id_kelas, k.kelas, km.id id_kamar, CONCAT(km.rayon,' ',km.nomor) kamar")
                          ->join(PREFIX_TABLE.'_pengguna_akses a','a.id_guru=g.id',"LEFT")
                          ->join(PREFIX_TABLE.'_list_akses la',"a.id_akses=la.id","LEFT")
                          ->join(PREFIX_TABLE.'_kelas k','k.id_walas=g.id','left')
                          ->join(PREFIX_TABLE.'_kamar km','km.id_wali_kamar=g.id','left')
                          ->where($where)
                          ->groupBy('g.id')
                          ->get()
                          ->getRow();
        // var_dump($data, $this->db->getLastQuery());

        $data_ortu = $this->db->table(PREFIX_TABLE.'_santri s')
                        ->select("s.id id_santri, s.nama nama_santri, 
                        s.ayah_nama, s.ayah_telepon, s.ayah_email,
                        s.ibu_nama, s.ibu_telepon, s.ibu_email,
                        s.wali_nama, s.wali_telepon, s.wali_email")
                        ->where('ayah_email', $email)
                        ->orWhere('ibu_email', $email)
                        ->orWhere('wali_email', $email)
                        ->get()
                        ->getRow();
        // var_dump($data_ortu, $this->db->getLastQuery());
        // var_dump($data
        if (empty($data_ortu) && empty($data)) return;
        if (empty($data)) {
          $data = $data_ortu;
          if ($email == $data_ortu->ayah_email)
            $data->nama = $data_ortu->ayah_nama;
          else if ($email == $data_ortu->ibu_email)
            $data->nama = $data_ortu->ibu_nama;
          else if ($email == $data_ortu->wali_email)
            $data->nama = $data_ortu->wali_nama;
          
          $data->app_roles['all'] = 'ortu';
          $data->akses = ['all' => []];
        } else {        
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

          $akses = $this->db->table(PREFIX_TABLE.'_pengguna_akses a')
                  ->select("la.*, a.*")
                  ->join(PREFIX_TABLE.'_list_akses la','a.id_akses=la.id')
                  ->orWhere($where)
                  ->get()
                  ->getResult();
          
          // var_dump($this->db->getLastQuery());
          foreach ($akses as $key => $value) {
            // $data->app_roles[$value->app] = $value->role;
            $data->app_roles[$value->app] = $value->role;
            $data->akses[$value->app][] = $value;
          }
        }

        if (!empty($data_ortu))
          $data->akses['all'][] = (object)[
              'nama_app' => 'Seluruh Aplikasi',
              'role' => 'ortu',
              'app' => 'all',
            ];

        return $data;
    }

    public function getAll(
        array $whereAnd = [], 
        array $whereOr = [], 
        array $whereIn = [], 
        array $orWhereIn = [], 
        array $groupBy = [], 
        string $order = '', 
        int $limit = 0, 
        int $offset = 0,  
        $relations = NULL,
        $pass_key = [],
        bool $return_data = FALSE)
    {
      $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;

      $data = $this->db->table(PREFIX_TABLE.'_guru p')
                    ->select("p.*, GROUP_CONCAT(la.nama_app,' ( ',a.role,' ) ' SEPARATOR ',    ') app")
                    ->join(PREFIX_TABLE.'_pengguna_akses a','a.id_guru=p.id')
                    ->join(PREFIX_TABLE.'_list_akses la','a.id_akses=la.id')
                    ->where($whereAnd)
                    ->groupBy('p.id')
                    ->orderBy($order)
                    ->get()
                    ->getResult();

      return $data;
    }

    
    public function getData($id)
    {
      $data = $this->db->table(PREFIX_TABLE.'_guru p')
                    ->select("p.*, GROUP_CONCAT(a.id_akses) as id_akses")
                    ->join(PREFIX_TABLE.'_pengguna_akses a','a.id_guru=p.id','left')
                    ->join(PREFIX_TABLE.'_list_akses la','a.id_akses=la.id','left')
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