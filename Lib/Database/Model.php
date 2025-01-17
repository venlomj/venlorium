<?php

namespace Lib\Database;

class Model extends \stdClass implements \JsonSerializable {
    protected string $primaryKey = "id";
    protected array $attributes = [];
    public array $hidden = [];
    public string $table = "";

    protected QueryBuilder $queryBuilder;

    public function __construct() 
    {
        $this->queryBuilder = new QueryBuilder($this);
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function fill(array $attributes): static {
        foreach ($attributes as $key => $value) {
            // Populate both the attributes array and existing properties
            $this->attributes[$key] = $value;
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }
    
    

    public function getAttributes(): array {
        return $this->attributes;
    }

    public function jsonSerialize(): mixed {
        return array_diff_key($this->attributes, array_flip($this->hidden));
    }

    // Static Methods for Querying
    public static function all(): array {
        $instance = new static();
        return $instance->queryBuilder->get();
    }

    public static function find(int|string $id): ?Model {
        $instance = new static();
        $result = $instance->queryBuilder->find($id, $instance->primaryKey);
        if ($result) {
            return $instance->fill($result);
        }
        return null;
    }

    public static function findById(int|string $id): ?Model {
        $model = new static();
        $conditions = [
            ["id", "=", $id],
        ];
    
        foreach ($conditions as $condition) {
            $model = $model->where($condition[0], $condition[1], $condition[2]);
        }
    
        return $model->first();
    }

    public function toArray(): array {
        return $this->getAttributes();
    }
    
    
    public static function where(string $field, string $operator, $value): QueryBuilder {
        $instance = new static();
        return $instance->queryBuilder->where($field, $operator, $value);
    }

    public static function select(string $fields = "*"): QueryBuilder {
        $instance = new static();
        return $instance->queryBuilder->select($fields);
    }

    public function save(): bool {
        if (isset($this->attributes[$this->primaryKey])) {
            // Update if the primary key exists
            return $this->queryBuilder->update($this->attributes[$this->primaryKey], $this->attributes);
        } else {
            // Insert if the primary key does not exist
            return $this->queryBuilder->insert($this->attributes);
        }
    }

    public static function delete(int|string $id): bool {
        $instance = new static();
        return $instance->queryBuilder->delete($id);
    }

    // Relationships
    public function hasMany(string $relatedModel, string $foreignKey): array {
        $relatedInstance = new $relatedModel();
        return $relatedInstance->queryBuilder
            ->where($foreignKey, '=', $this->{$this->primaryKey})
            ->get();
    }

    public function belongsTo(string $relatedModel, string $foreignKey): ?Model {
        $relatedInstance = new $relatedModel();
        $result = $relatedInstance->queryBuilder
            ->where($relatedInstance->primaryKey, '=', $this->{$foreignKey})
            ->first();
        return $result ? $relatedInstance->fill($result) : null;
    }

    public function hasOne(string $relatedModel, string $foreignKey): ?Model {
        $relatedInstance = new $relatedModel();
        $result = $relatedInstance->queryBuilder
            ->where($foreignKey, '=', $this->{$this->primaryKey})
            ->first();
        return $result ? $relatedInstance->fill($result) : null;
    }
}
