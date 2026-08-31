<?php

namespace App\Models;

use CodeIgniter\Model;

class KolomModel extends Model
{
    protected $table         = PREFIX_TABLE.'_nama_kolom';
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

    public function insertToGroup($data)
    {
        $this->db->table(PREFIX_TABLE.'_nama_tabel')->insert($data);

        return $this->db->insertID();
    }

    public function getAll($nama_tabel, $input = TRUE, $output = FALSE)
    {
        return $this->db->table(PREFIX_TABLE.'_nama_kolom n')
                    ->select('gk.*, n.*')
                    ->join(PREFIX_TABLE.'_nama_tabel gk','gk.id=n.id_group')
                    ->groupStart()
                        ->where('nama_tabel',$nama_tabel)
                        ->orWhere('alias_tabel',$nama_tabel)
                    ->groupEnd()
                    ->where($input ? "from_user='1'" : "1=1")
                    ->where($output ? "input_only='0'" : "1=1")
                    ->orderBy('gk.id, n.order')
                    ->get()
                    ->getResult();
    }
    
    public function getKolom($nama_tabel, $nama_kolom)
    {
        return $this->db->table(PREFIX_TABLE.'_nama_kolom n')
                    ->select('gk.*, n.*')
                    ->join(PREFIX_TABLE.'_nama_tabel gk','gk.id=n.id_group')
                    ->where('nama_tabel',$nama_tabel)
                    ->where('nama_kolom',$nama_kolom)
                    ->orderBy('gk.id, n.order')
                    ->get()
                    ->getRow();
    }

    
    public function getModelName($nama_tabel)
    {
        return $this->db->table(PREFIX_TABLE.'_nama_tabel n')
                    ->where('nama_tabel',$nama_tabel)
                    ->get()
                    ->getRow()->model ?? '';
    }

    public function getDefaultNamaKolom($nama_tabel)
    {
        $datas = $this->db->table('INFORMATION_SCHEMA.COLUMNS')
                          ->select("COLUMN_NAME nama_kolom, DATA_TYPE tipe, DATA_TYPE input, '1' sortable")
                          ->where('TABLE_SCHEMA',getenv('database.default.database'))
                          ->where('TABLE_NAME', $nama_tabel)
                          ->whereNotIn('COLUMN_NAME',['id', 'updated_at','created_at','updated_by','created_by'])
                          ->orderBy('ORDINAL_POSITION')
                          ->get()
                          ->getResult();
        
        return $datas;
    }
}