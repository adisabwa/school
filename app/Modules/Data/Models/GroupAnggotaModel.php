<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class GroupAnggotaModel extends Model
{
    protected $table         = 'mu_group_anggota';
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

    
    public function getAll($whereAnd = [], $whereOr = [], $order = '', $limit = 0, $offset = 0)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('mu_group_anggota qb')
                    ->select("qb.*, s.nama")
                    ->join('mu_anggota s','qb.id_anggota=s.id')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->limit($limit, $offset)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    public function getTableName()
    {
        return $this->table;
    }

}