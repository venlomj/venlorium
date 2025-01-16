<?php

namespace App\Lib;

class Model extends \stdClass implements \JsonSerializable {
    protected string $primaryKey = "id";
    private array $attributes = [];
    private ?string $query = null;
    public array $hidden = [];
    public string $table = "";

    public QueryBuilder $queryBuilder;

    public function __construct() 
    {
        $this->queryBuilder = new QueryBuilder($this);
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __get($name) {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function getAttributes(): array {
        return $this->attributes;
    }


    public function getTable(): string
    {
        return $this->table;
    }


    public function getQuery(): string {
        return $this->query;
    }

    public function jsonSerialize(): mixed
    {
        return array_filter($this->attributes, function($value, $key) {
            return !in_array($key, $this->hidden);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function with(array $relations): QueryBuilder {
        $model = new static();
        return $model->queryBuilder->with($relations);
    }

    public static function paginate(int $page = 1, int $limit = 10) {
        $object = new static();

        return $object->queryBuilder->paginate($page, $limit);
    }

    public static function where(array $conditions): QueryBuilder {
        $model = new static();
        $model->queryBuilder->where($conditions);
        return $model->queryBuilder;
    }

    public static function findById(int $id): ?Model {
        $model = new static();
        $result = $model->where([
            ["id"=> $id]
        ])->first();
        return $result;
    }

    public static function all() {
        $object = new static();
        return $object->queryBuilder->get();
    }

    public static function select(string $column = "*"): QueryBuilder {
        $model = new static();
        return $model->queryBuilder->select($column);
    }

    
    public static function delete() {
        $object = new static();
        return $object->queryBuilder->delete();
    }
    
    public function save() {
        $this->queryBuilder->save();
    }
    
    public function hasMany($related, $foreignKey)
    {
        $relatedModel = new $related;
        $relatedTable = $relatedModel->table;

        $foreignKeyValue = $this->{$foreignKey};
    
        // Build the query to fetch related records
        $this->queryBuilder->from($relatedTable)
                        ->where([$foreignKey, '=', $foreignKeyValue]);

        return $this->queryBuilder->get(); // Return multiple related models
    }

    public function belongsTo(string $relatedModel, string $foreignKey)
    {
        $relatedModelInstance = new $relatedModel();
        $relatedTable = $relatedModelInstance->table;
        $foreignValue = $this->{$foreignKey};

        // Query the related table using the foreign key
        $this->queryBuilder->from($relatedTable)
                        ->where([$relatedModelInstance->primaryKey, '=', $foreignValue]);

        return $this->queryBuilder->first(); // Return the single related model
    }

    public function hasOne(string $relatedModel, string $foreignKey)
    {
        $relatedModelInstance = new $relatedModel();
        $relatedTable = $relatedModelInstance->table;
        $foreignKeyValue = $this->{$this->primaryKey}; // Use primary key of current model

        // Query the related table to get the first record
        $this->queryBuilder->from($relatedTable)
                        ->where([$foreignKey, '=', $foreignKeyValue]);

        return $this->queryBuilder->first(); // Return the single related model
    }

}