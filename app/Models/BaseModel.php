<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $table      = 'table';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];
    public $userId;
    public $relations = [];

    public function __construct()
    {
        parent::__construct();

        helper('auth');
        $this->userId = userdata()->id_guru ?? (userdata()->id ?? 0) * -1; // adjust if using another auth system
    }

    protected function initialize()
    {
        // $this->db = $this->builder();
    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    public function getOptionsData($where = [], callable $concatFunc)
    {
      $options = [];
      $data = $this->where($where)
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $options[] = (object)[
          'value' => "$d->id",
          'label' => $concatFunc($d)
        ];
      }
      return $options;
    }

    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = $this->userId ?? null;

        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = $this->userId ?? null;

        return $data;
    }

    public function getAll(array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], string $order = '', int $limit = 0, int $offset = 0)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;
        
        // var_dump($whereAnd);
        $data = $this->db->table($this->table)
                    ->select("{$this->table}.*")
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd();

        foreach ($this->relations as $key => $rel) {
            $data->select($rel['select']);
            $condition = [
                "$this->table.".$rel['foreign_key']."=".$rel['table'].".".($rel['local_key'] ?? 'id'),
                $rel['condition'] ?? "1=1"
            ];
            $data->join($rel['table'], implode(" AND ", $condition), $rel['type']);
        }

        foreach ($whereIn as $key => $value) {
            $data->whereIn($key, $value);
        }
        
        foreach ($orWhereIn as $key => $value) {
            $data->orWhereIn($key, $value);
        }

        $data = $data->orderBy($order)
                    ->limit($limit, $offset)
                    ->get()
                    ->getResult();

        return $data;
    }

    public function applyJoin(object $builder, array $relations = [])
    {
        foreach ($relations as $key => $relation) {
            $table = $this->relations[$relation];
            
        }
    }
}