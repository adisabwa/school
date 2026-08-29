<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $table      = 'table';
    protected $table_label = 'LENGKAP';
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

        helper('auth,security');
        $this->userId = userdata()->id ?? -1; // adjust if using another auth system
    }

    public function reset()
    {
        return new static();
    }

    protected function initialize()
    {
        // $this->db = $this->builder();
    }
    
    public function getTableName()
    {
        return $this->table;
    }

    public function getTableLabel()
    {
        return $this->table_label;
    }
    
    public function getOptionsData(array $where = [], ?callable $concatFunc = null, ?callable $addOptions = null, string $order = '', $groupBy = [], $columnValue = 'id')
    {
      helper('security');
      $options = [];
      if (empty($groupBy)) {
        $groupBy = ['id'];
      }
    //   var_dump($where, $groupBy);
    //   $this->selects = ['sum({f}.id)'];
    //   var_dump($this->selects);
      $result = $this->getAll(whereAnd: $where, groupBy: $groupBy, order: $order);
        // var_dump($result);
        // exit;
      foreach ($result as $key => $d) {
        $option = (object)[
          'value' => encrypt_id($d->$columnValue),
            // 'value' => "$d->id",
          'label' => $concatFunc ? $concatFunc($d) : $d->nama,
        ];
        $option = $addOptions ? $addOptions($option, $d) : $option;
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

    public function addTableBefore(string $table, $attr, $is_key = FALSE)
    {
        // var_dump($attr);
        $attr = is_array($attr) ? $attr : explode(',', $attr);
        $new_attr = [];
        foreach ($attr as $key => $value) {
            $old_value = $value;
            if ($value === NULL)
                continue;

            $value = $is_key ? $key : $value;
            if (!is_numeric($value)) {
                if (is_array($value))
                    $value = $key;

                $no_line = strpos($value, '{n}');
                $pos2 = strpos($value, '{f}.');
                $pos = strpos($value, '{f}');
                // var_dump('pos', $value, $no_line, $pos2, $pos);
                if ($no_line !== false) {
                    $new = str_replace('{n}','', $value);
                } else if ($pos2 !== false) {
                    $new = str_replace('{f}',$table, $value);
                } else if ($pos !== false) {
                    $new = str_replace('{f}',"$table.", $value);
                } else {
                    $new = "$table.$value";
                }
                // var_dump($value, $new);
                if ($is_key && $old_value !== '')
                    $new_attr[$new] = $old_value === NULL ? NULL : $old_value;
                else
                    $new_attr[] = $new;
            } else {
                $new_attr[$old_value] = NULL;
            }
        }
        // var_dump($new_attr);
        return $new_attr;
    }

    public function getAll(
        array $whereAnd = [], 
        array $whereOr = [], 
        array $whereIn = [], 
        array $orWhereIn = [], 
        array $groupBy = [], 
        array $havingAnd = [], 
        string $order = '', 
        int $limit = 0, 
        int $offset = 0,  
        $relations = NULL,
        $pass_key = [],
        bool $return_data = FALSE)
    {

        // var_dump($whereAnd, $whereOr);
        // var_dump('Where', $this->table, $whereAnd, $whereOr);
        
        // var_dump($whereAnd);echo "<br/>";
        $data = $this->db->table($this->table)
                    ->select("{$this->table}.*")
                    ->select($this->addTableBefore($this->table, $this->selects))
                    ->orderBy($order);
        
        if (!empty($whereAnd)) {
            $whereAnd = $this->addTableBefore($this->table, $whereAnd, TRUE);
            // var_dump($whereAnd);
            foreach($whereAnd as $field => $_wa) {
                is_numeric($field) ? $data->where($_wa) : $data->where($field, $_wa);
            }
        }

        if (!empty($whereOr)) {
            $data->groupStart();
            $whereOr = $this->addTableBefore($this->table, $whereOr, TRUE);
            foreach($whereOr as $field => $_wo) {
                is_numeric($field) ? $data->orWhere($_wo) : $data->orWhere($field, $_wo);
            }
            $data->groupEnd();
        }
        
        $data->groupBy($groupBy)
             ->having($havingAnd);
        // var_dump($groupBy);
        // var_dump('pass_key', $pass_key);
        $this->applyJoin($data, $relations, $pass_key);
        
        foreach ($whereIn as $key => $value) {
            $data->whereIn("$this->table.$key", $value);
        }
        
        foreach ($orWhereIn as $key => $value) {
            $data->orWhereIn("$this->table.$key", $value);
        }

        $data->limit($limit, $offset);

        if ($return_data)
            return $data->getCompiledSelect();
        // var_dump($data->getCompiledSelect());
        // exit;

        return $data->get()->getResult();
    }

    public function getDataWhere(
        array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], 
        array $havingAnd = [], 
        string $order = '', int $limit = 0, int $offset = 0)
    {
        $data = $this->getAll(whereAnd: $whereAnd, whereOr: $whereOr, whereIn: $whereIn, orWhereIn: $orWhereIn, 
            havingAnd: $havingAnd,
            order: $order, limit: $limit, offset: $offset);

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
        $data = $this->getAll(whereAnd: ['{f}id' => $id]);
        
        if ($data) {
            return $data[0];
        } else {
            return [];
        }
    }

    public function applyJoin(object $builder, $relations = NULL, $pass_key = [])
    {
        $currTable = $this->table;
        // var_dump($this->relations);
        // exit;
        if ($relations === NULL) {
            $relations = $this->relations;
        } else {
            $relations = array_merge($relations, $this->relations);
        }
        // var_dump($relations);exit;
        // if (count($pass_key) > 0) {
        //     // var_dump('pass_key', $pass_key);
        //     var_dump($relations);exit;
        // }
        foreach ($relations as $key => $rel) {
            if ($rel) {
                if (in_array($key, $pass_key)) continue;
                // var_dump($relation, $rel);
                $model = false;
                $table = $rel['table'] ?? '';
                $model = $rel['model'] ?? NULL;
                $alias = $rel['alias'] ?? NULL;
                $condition = [];

                if ($model) {
                    $model = model($rel['model']);
                    $table = $model->getTableName();
                    $table_alias = $alias ?? "alias_$table";
                    // $condition = [
                    //     "$currTable.".$rel['foreign_key']."=".$table_alias.".".($rel['local_key'] ?? 'id')
                    // ];
                    // if ($rel['on_condition'] ?? false)
                    //     $condition = $this->addTableBefore($table_alias, $rel['on_condition']);
                    
                    if ($rel['order'] ?? false) {
                        $j_order = [];
                        if (!is_array($rel['order'])) {
                            $j_order = explode(',', $rel['order']);
                        }
                        // $j_order = $this->addTableBefore($table_alias, $rel['order']);
                        $j_order = implode(",", $j_order);
                        // $builder->orderBy($j_order);
                    }
                    
                    // if ($rel['pass_key'] ?? false) {
                    //     var_dump('join', $pass_key, $rel['pass_key'], $rel['local_key'], in_array($rel['local_key'], $pass_key));
                    //     echo "<br/>";
                    //     // exit;
                    // }
                    if ($rel['inner_selects'] ?? false) {
                        $model->selects = array_merge($model->selects, $rel['inner_selects']);
                    }
                    $query = $model->getAll(
                        whereAnd:($rel['condition'] ?? []), 
                        return_data: TRUE, 
                        order: ($j_order ?? ''), 
                        pass_key: $rel['pass_key'] ?? [],
                        groupBy: ($rel['group_by'] ?? [])
                    );
                    // var_dump($rel['on_condition'] ?? [], $query);
                    // if ($rel['pass_key'] ?? false) {
                    //     var_dump('query', $pass_key, $rel['pass_key'], $rel['local_key'], in_array($rel['local_key'], $pass_key));
                    //     var_dump($query);
                    //     echo "<br/>";
                    //     exit;
                    // }
                    if (!empty($rel['selects']))
                        $builder->select($this->addTableBefore($table_alias, $rel['selects']));

                    if ($rel['foreign_key'] ?? false) {
                        $condition = [
                            "$currTable.".$rel['foreign_key']."=".$table_alias.".".($rel['local_key'] ?? 'id')
                        ];
                    }
                    if ($rel['on_condition'] ?? false)
                        $condition = array_merge($condition,
                        $this->addTableBefore($table_alias, $rel['on_condition'])
                    );
                    // var_dump($condition, implode(" AND ", $condition));
                    $builder->join("($query) $table_alias", 
                        implode(" AND ", $condition),
                        $rel['type'] ?? 'inner'
                    );
                } else {
                    $table_alias = $alias ?? $table;
                    if (!empty($rel['selects']))
                        $builder->select($this->addTableBefore($table_alias, $rel['selects']));
                    // var_dump($this->addTableBefore($table_alias, $rel['selects']));
                    // echo "<br/>";
                    if ($rel['foreign_key'] ?? false)
                        $condition = [
                            "$currTable.".$rel['foreign_key']."=".$table_alias.".".($rel['local_key'] ?? 'id')
                        ];
                        
                    if ($rel['on_condition'] ?? false)
                        $condition = array_merge($condition,
                        $this->addTableBefore($table_alias, $rel['on_condition'])
                    );
                    $text_table = $alias ? "$table $alias" : $table;
                    // var_dump($condition, $text_table, is_array($condition), $rel['type'] ?? 'inner');echo "<br/>";
                    // var_dump($condition, implode(" AND ", $condition), $rel['on_condition'] ?? '',$text_table, is_array($condition), $rel['type'] ?? 'inner');echo "<br/>";
                    $builder->join($text_table, implode(" AND ", $condition), $rel['type'] ?? 'inner');
                    if ($rel['order'] ?? false) {
                        if (!is_array($rel['order'])) {
                            $j_order = explode(',', $rel['order']);
                        }
                        $j_order = $this->addTableBefore($table_alias, $rel['order']);
                        $j_order = implode(",", $j_order);
                        $builder->orderBy($j_order);
                    }
                    if ($rel['group_by'] ?? false) {
                        $g_by = $this->addTableBefore($table_alias, $rel['group_by']);
                        $builder->groupBy($g_by);
                    }
                }
            }
        }

        // var_dump($builder->getCompiledSelect());
        // exit;
        return $builder;
    }
    
    public function addRelations($relations)
    {
        // var_dump($relations);
        foreach ($relations as $key => $relation) {
            if ($this->relations[$key] ?? false){
                foreach($relation as $index => $value) {
                    $this->relations[$key][$index] = $value;
                }
            }
        }
        // var_dump($key, $this->relations[$key]);
        return $this;
    }
    
    public function resolveConditions($condition)
    {
        
    }
    public function addConditions($conditions)
    {
        // var_dump($conditions);
        foreach ($conditions as $key => $condition) {
            if (empty($this->relations[$key]['condition'])) {
                $this->relations[$key]['condition'] = [];
            }
            foreach($condition as $index => $value) {
                if (empty($value)) {
                    $cond = $index;
                } else {
                    $cond = "$index='$value'";
                }
                $this->relations[$key]['condition'][] = $cond;
            }
        }
        // var_dump($this->relations);
        return $this;
    }
    
}