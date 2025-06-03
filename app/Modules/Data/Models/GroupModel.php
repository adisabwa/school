<?php

namespace Modules\Data\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $table         = 'mu_group';
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

        $subQuery =  $this->db->table('mu_group g')
                              ->select('g.*')
                              ->join('mu_group_anggota ga','ga.id_group=g.id')
                              ->where($whereAnd)
                              ->groupStart()
                                  ->orWhere($whereOr)
                              ->groupEnd()
                              ->groupBy('g.id')
                              ->limit($limit, $offset);

        $data = $this->db->table('mu_group_anggota ga')
                    ->select("ga.*, g.*, ga.id id_ga, s.nama")
                    ->join("({$subQuery->getCompiledSelect()}) g",'ga.id_group=g.id')
                    ->join('mu_anggota s','ga.id_anggota=s.id')
                    // ->where($whereAnd)
                    // ->groupStart()
                    //     ->orWhere($whereOr)
                    // ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    
    public function getData($id)
    {
        $data = $this->db->table('mu_group_anggota ga')
                    ->select("ga.*, g.*, ga.id id_ga, s.nama")
                    ->join("mu_group g",'ga.id_group=g.id')
                    ->join('mu_anggota s','ga.id_anggota=s.id')
                    ->where("g.id", $id)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    public function getTableName()
    {
        return $this->table;
    }
    
    public function getOptions($where = [])
    {
      $options = [];
      $data = $this->db->table('mu_group p')
                    ->select('*')
                    ->where($where)
                    ->get()
                    ->getResult();
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => "$d->nama_group"
        ];
      }
      return $options;
    }
}