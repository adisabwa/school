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
    protected $beforeInsertBatch = ['setCreatedBatchBy'];
    protected $beforeUpdate = ['setUpdatedBy'];
    public $userId;
    public $relations = [];
    public $selects = [];

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
    
    public function getOptionsData($where = [], ?callable $concatFunc = null, ?callable $addOptions = null)
    {
      $options = [];
      $data = $this->where($where)
                    ->get()
                    ->getResult();
                    
      foreach ($data as $key => $d) {
        $option = (object)[
          'value' => "$d->id",
          'label' => $concatFunc($d) ?? $d->nama
        ];
        if ($addOptions && is_callable($addOptions)) {
            $option = $addOptions($option, $d);
        }
        $options[] = $option;
      }
      return $options;
    }

    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = $this->userId ?? null;

        return $data;
    }

    protected function setCreatedBatchBy(array $data)
    {
        $id = $this->userId ?? null;
        $data['data'] = array_map(function($item) use ($id) {
            $item['created_by'] = $id; // mulai dari 1
            return $item;
        }, $data['data'], array_keys($data['data']));

        // var_dump($data['data']);exit;
        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = $this->userId ?? null;

        return $data;
    }

    public function addTableBefore(string $table, $attr)
    {
        $attr = is_array($attr) ? $attr : explode(',', $attr);
        $new_attr = [];
        foreach ($attr as $key => $value) {
            if (empty($value))
                continue;
            $pos = strpos($value, '{f}');
            if ($pos !== false) {
                $new_attr[] = str_replace('{f}',$table, $value);
            } else {
                $new_attr[] = "$table.$value";
            }
        }
        return $new_attr;
    }

    public function getAll(
        array $whereAnd = [], 
        array $whereOr = [], 
        array $whereIn = [], 
        array $orWhereIn = [], 
        string $order = '', 
        int $limit = 0, 
        int $offset = 0,  
        $relations = NULL)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;
        
        // var_dump($whereAnd, $relations);
        $data = $this->db->table($this->table)
                    ->select("{$this->table}.*")
                    ->select($this->addTableBefore($this->table, $this->selects))
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

        $data->limit($limit, $offset);

        // var_dump($data->getCompiledSelect());

        return $data->get()->getResult();
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
        // var_dump($id);
        $data = $this->getAll(whereAnd: ['id' => $id]);

        // var_dump($this->db->getLastQuery());
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
                $alias = $rel['alias'] ?? NULL;
                $table_alias = $alias ?? $table;
                if ($rel['model'] ?? false) {
                    $model = model($rel['model']);
                    $table = $model->getTableName();
                }

                $builder->select($this->addTableBefore($table_alias, $rel['selects']));
                // var_dump($this->addTableBefore($table_alias, $rel['selects']));
                // echo "<br/>";
                $condition = [
                    "$this->table.".$rel['foreign_key']."=".$table_alias.".".($rel['local_key'] ?? 'id')
                ];
                if ($rel['condition'] ?? false)
                    $condition = array_merge($condition,
                    $this->addTableBefore($table_alias, $rel['condition'])
                );

                $text_table = $alias ? "$table $alias" : $table;
                // var_dump($condition, $text_table, is_array($condition), $rel['type'] ?? 'inner');echo "<br/>";
                // var_dump(implode(" AND ", $condition), $text_table, is_array($condition), $rel['type'] ?? 'inner');echo "<br/>";
                $builder->join($text_table, implode(" AND ", $condition), $rel['type'] ?? 'inner');
                if ($rel['order'] ?? false) {
                    if (!is_array($rel['order'])) {
                        $j_order = explode(',', $rel['order']);
                    }
                    $j_order = $this->addTableBefore($table_alias, $rel['order']);
                    $j_order = implode(",", $j_order);
                    $builder->orderBy($j_order);
                }

                if ($model) {
                    $builder = $model->applyJoin($builder);
                }
            }
        }

        // var_dump($builder->getCompiledSelect());
        // exit;
        return $builder;
    }
    
    public function addRelations($relations)
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