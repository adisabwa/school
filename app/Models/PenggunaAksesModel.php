<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaAksesModel extends Model
{
    protected $table         = 'sch_pengguna_akses';
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

    
    public function getAll($whereAnd = [], $whereOr = [], $order = '')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('sch_pengguna_akses i')
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
}