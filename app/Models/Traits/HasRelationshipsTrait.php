<?php

namespace App\Models\Traits;

trait HasRelationshipsTrait
{
    protected $with = [];

    public function withRelations(array $relations): static
    {
        $this->with = $relations;
        return $this;
    }

    protected function applyJoins(object $builder, array $selects, array $conditions)
    {
        foreach ($this->with as $relationName) {
            if (!isset($this->relations[$relationName])) {
                continue;
            }

            $relation = $this->relations[$relationName];

            $relatedModel = new $relation['model'];
            $relatedTable = $relatedModel->table ?? $relatedModel->getTable();

            switch ($relation['type']) {
                case 'belongsTo':
                case 'hasOne':
                    $on = "{$this->table}.{$relation['foreign_key']} = {$relatedTable}.{$relation['owner_key']}";
                    $builder->join($relatedTable, $on, $relation['join'] ?? 'left');
                    $builder->select("{$relatedTable}.*");
                    break;
                // hasMany handled after fetch
            }
        }

        return $builder;
    }

    public function findAllWithRelations(int $limit = 0, int $offset = 0)
    {
        $builder = $this->builder();
        $builder->select("{$this->table}.*");

        $this->applyJoins($builder);

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        $results = $builder->get()->getResultArray();

        return $this->eagerLoadHasMany($results);
    }

    protected function eagerLoadHasMany(array $results): array
    {
        if (empty($results)) return $results;

        foreach ($this->with as $relationName) {
            if (!isset($this->relations[$relationName])) continue;

            $relation = $this->relations[$relationName];

            if ($relation['type'] !== 'hasMany') continue;

            $relatedModel = new $relation['model'];
            $foreignKey = $relation['foreign_key'];
            $localKey = $relation['local_key'];

            $localIds = array_column($results, $localKey);
            $children = $relatedModel->whereIn($foreignKey, $localIds)->findAll();

            // Group by local key
            $grouped = [];
            foreach ($children as $child) {
                $grouped[$child[$foreignKey]][] = $child;
            }

            // Attach to parent
            foreach ($results as &$result) {
                $result[$relationName] = $grouped[$result[$localKey]] ?? [];
            }
        }

        return $results;
    }
}
