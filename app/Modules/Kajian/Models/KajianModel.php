<?php

namespace Modules\Kajian\Models;

use CodeIgniter\Model;

class KajianModel extends Model
{
    protected $table         = 'mu_kajian';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected function initialize()
    {

    }

    public function getAll($whereAnd = [], $whereOr = [], $order = '', $limit = 0, $offset = 0, $groupBy = ['id'], $whereIn = [])
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $builder = $this->db->table('mu_kajian f')
                    ->select('f.*, 1 as data_chart, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where($whereAnd);

        foreach($whereIn as $key => $in) {
            $builder->whereIn($key, $in);
        }

        return $builder->groupStart()
                            ->orWhere($whereOr)
                        ->groupEnd()
                        ->orderBy($order)
                        ->groupBy($groupBy)
                        ->limit($limit, $offset)
                        ->get()
                        ->getResult();
    }

    public function getOptionsTipe()
    {
        $data = $this->db->table($this->table)
                        ->select('*')
                        ->groupBy('tipe')
                        ->get()
                        ->getResult();
        
        $options = [];
        foreach ($data as $key => $val) {
            $options[] = (object)[
                'value' => $val->tipe,
                'label' => ucfirst($val->tipe),
            ];
        }

        if (empty($options)) 
            $options = [[
                'value' => 'kajian',
                'label' => 'Kajian',
            ]];


        return $options;
    }

    public function getTableName()
    {
        return $this->table;
    }
}