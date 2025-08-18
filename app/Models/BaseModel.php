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

    public function addTableBefore(string $table, array $attr)
    {
        $new_attr = [];
        foreach ($attr as $key => $value) {
            $pos = strpos($value, '{f}');
            if ($pos !== false) {
                $new_attr[] = str_replace('{f}',$table, $value);
            } else {
                $new_attr[] = "$table.$value";
            }
        }
        return $new_attr;
    }

    public function getAll(array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], string $order = '', int $limit = 0, int $offset = 0,  $relations = NULL)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;
        
        // var_dump($whereAnd, $relations);
        $data = $this->db->table($this->table)
                    ->select("{$this->table}.*")
                    ->orderBy($order)
                    ->having($whereAnd)
                    ->havingGroupStart()
                        ->orHaving($whereOr)
                    ->havingGroupEnd();

        $this->applyJoin($data, $relations);

        foreach ($whereIn as $key => $value) {
            $data->havingIn($key, $value);
        }
        
        foreach ($orWhereIn as $key => $value) {
            $data->orHavingIn($key, $value);
        }

        $data = $data->limit($limit, $offset)
                    ->get()
                    ->getResult();

        return $data;
    }

    public function getDataWhere(array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], string $order = '', int $limit = 0, int $offset = 0)
    {
        $data = $this->getAll(whereAnd: $whereAnd, whereOr: $whereOr, whereIn: $whereIn, orWhereIn: $orWhereIn, order: $order, limit: $limit, offset: $offset);

        // var_dump($whereAnd, $data);
        if ($data) {
            return $data[0];
        } else {
            return [];
        }
    }
    
    public function getData($id)
    {
        $data = $this->getAll(whereAnd: ['id' => $id]);
        
        if ($data) {
            return $data[0];
        } else {
            return [];
        }
    }

    public function applyJoin(object $builder, $relations = NULL)
    {
        // var_dump($relations);
        // exit;
        if ($relations === NULL) {
            $relations = array_keys($this->relations);
        }
        // var_dump($relations);
        foreach ($relations as $key => $relation) {
            $rel = $this->relations[$relation];
            if ($rel) {
                // var_dump($relation, $rel);
                $model = false;
                $table = $rel['table'];
                if ($rel['model'] ?? false) {
                    $model = model($rel['model']);
                    $table = $model->getTableName();
                }

                $builder->select($this->addTableBefore($table, $rel['selects']));
                $condition = [
                    "$this->table.".$rel['foreign_key']."=".$table.".".($rel['local_key'] ?? 'id')
                ];
                if ($rel['condition'] ?? false)
                    $condition[] = $rel['condition'];

                $builder->join($table, implode(" AND ", $condition), $rel['type'] ?? 'inner');
                if ($rel['order'] ?? false) {
                    $j_order = $this->addTableBefore($table, $rel['order']);
                    $j_order = implode(",", $j_order);
                    $builder->orderBy($j_order);
                }

                if ($model) {
                    $builder = $model->applyJoin($builder);
                }
            }
        }

        return $builder;
    }

    public function addRelation($relations)
    {
        foreach ($relations as $key => $relation) {
            if ($this->relations[$key] ?? false){
                foreach($relation as $index => $value) {
                    $this->relations[$key][$index] = $value;
                }
            }
        }

        return $this;
    }
    
}