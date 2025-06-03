<?php

namespace App\Models;

use CodeIgniter\Model;

class KolomModel extends Model
{
    protected $table         = 'mu_nama_kolom';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'created_at';

    protected function initialize()
    {

    }

    public function getAll($nama_tabel, $input = TRUE, $output = FALSE)
    {
        return $this->db->table('mu_nama_kolom n')
                    ->select('gk.*, n.*')
                    ->join('mu_nama_tabel gk','gk.id=n.id_group')
                    ->where('nama_tabel',$nama_tabel)
                    ->where($input ? "from_user='1'" : "1=1")
                    ->where($output ? "input_only='0'" : "1=1")
                    ->orderBy('n.order')
                    ->get()
                    ->getResult();
    }

    
    public function getKolom($nama_tabel, $nama_kolom)
    {
        return $this->db->table('mu_nama_kolom n')
                    ->select('gk.*, n.*')
                    ->join('mu_nama_tabel gk','gk.id=n.id_group')
                    ->where('nama_tabel',$nama_tabel)
                    ->where('nama_kolom',$nama_kolom)
                    ->orderBy('gk.id, n.order')
                    ->get()
                    ->getRow();
    }
}