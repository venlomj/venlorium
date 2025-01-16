<?php

namespace App\Lib;

class QueryBuilder {
    public ?string $table = null;
    private bool $has_select = false;
    private \PDO $pdo;
    private array $values = [];
    private array $attributes = [];
    private array $relations = [];
    private ?Model $model;
    public ?string $query = null;

    // private ?string $paginationPreQuery = null;
    // private ?string $paginationPostQuery = null;

    protected $paginationPreQuery = ''; // Initialize it here
    protected $paginationPostQuery = '';

    public function __construct(Model $model = null)
    {
        $this->query = "";
        $this->pdo = Database::$pdo;
        $this->model = $model;
        if ($model) {
            $this->table = $model->table;
        }
    }
    
    public function setValue(string $key, $value): void {
        $this->values[$key] = $value;
    }
    
    public function getValue(string $key) {
        return $this->values[$key] ?? null;
    }
    public function from(string $table): QueryBuilder {
        $this->table = $table;
        return $this;
    }

    public function with(array $relations): QueryBuilder {
        foreach ($relations as $relation) {
            if (is_string($relation)) {
                // Split the relation into parts
                $parts = explode(".", $relation);
    
                // Check if there are valid parts
                if (count($parts) > 0) {
                    $this->relations[] = $parts; // Add the parts
                } else {
                    // Handle invalid relations, e.g., throw an exception or log a warning
                    throw new \Exception("Invalid relation format: $relation");
                }
            } else {
                // Handle case when $relation is not a string
                throw new \Exception("Relation must be a string.");
            }
        }
        return $this;
    }
    
    
    
    private function addJoin(array $parts) {
        $relation = $parts[0]; // First part of the relation
    
        // Assuming $this->model has a method that returns the related model
        $relatedModel = $this->model->{$relation}(); 
    
        // Ensure the related model has a table name (e.g., `roles`)
        $relatedTable = $relatedModel->getTable();
    
        // Foreign key naming convention (assuming 'model_name_id')
        $foreignKey = $relation . "_id";
    
        // Ensure valid foreign key and related table are set
        if (!empty($relatedTable) && !empty($foreignKey)) {
            $this->query .= " JOIN $relatedTable ON $relatedTable.$foreignKey = {$this->table}.id";
        } else {
            throw new \Exception("Invalid relation or foreign key: $relation");
        }
    
        // Recursively handle nested relations
        if (count($parts) > 1) {
            array_shift($parts); // Remove the first part
            $this->addJoin($parts); // Recursively call for nested relations
        }
    }
    
    
    
    // public function with(array $relations): QueryBuilder {
    //     foreach ($relations as $relation) {
    //         if (is_string($relation)) {
    //             $parts = explode(".", $relation);
    //             $this->relations[] = $parts;
    //         }
    //     }
    //     return $this;
    // }

    public function execute() {
        try {
            $stmt = $this->pdo->prepare($this->query);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            Response::json(["error"=> $e->getMessage()], 500);
        }
    }

    public function save() {
        $this->attributes = $this->model->getAttributes();

        // Append timestamps
        if ($this->model->timestamps) {
            $this->includeTimestamps();
        }

        if (isset($this->attributes["id"])) {
            // Update
            $this->update()->where(["id", "=", $this->attributes["id"]])
            ->execute();
        } else {
            // $this->insert()->execute();
            $this->model->id = $this->pdo->lastInsertId();
        }
    }

    public function where($data) {
        $has_where = false;
        $sql = "";

        foreach ($data as $where) {
            $column = $where[0];
            $operator = $where[1];
            $value = $where[2];

            $this->values[] = $value;


            if (!$this->has_select) {
                $sql .= "SELECT * FROM " . $this->table;
                $this->has_select = true;
            }

            if (!$has_where) {
                $sql .= " WHERE " . $column . " " . $operator . " ?";
                $has_where = true;
            } else {
                $sql .= " AND ". $column . " ". $operator . " ?";
            }
        }

        $this->query = $sql;

        if ($this->$paginationPreQuery) {
            $this->paginationPreQuery = $sql;
        }
        return $this;
    }

    public function insert(): QueryBuilder {
        $query = "INSERT INTO {$this->table} (";
        $query .= implode(", ", array_keys($this->attributes));
        $query .= ") VALUES (";
        $query .= implode(", ", array_fill(0, count($this->attributes),"?"));
        $query .= ")";
        $this->query = $query;
        $this->values = array_values($this->attributes);

        return $this;
    }

    public function update(): QueryBuilder {
        $query = "UPDATE {$this->table} SET ";
        $query .= implode(", ", array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($this->attributes)));
        $this->values = array_values($this->attributes);
        return $this;
    }

    public function select(string $column = "*") {
        $this->query = "SELECT $column" . " FROM " . $this->table;
        $this->has_select = true;
        return $this;
    }

    public function paginate(int $page, int $limit) {
        if (!$this->has_select) {
            $this->select(); // Make sure the select query is available
        }
    
        $this->paginationPreQuery = $this->query; // Save the base query
    
        // Calculate the pagination offset
        $offset = ($page - 1) * $limit;
        $this->paginationPostQuery = " LIMIT $limit OFFSET $offset"; // Add LIMIT and OFFSET for pagination
    
        // Combine the base query with pagination
        $this->query = $this->paginationPreQuery . $this->paginationPostQuery;
    
        return $this;
    }
    
    

    public function orWhere(array $conditions): QueryBuilder {
        $has_where = false;
        $this->query .= " OR ";

        foreach ($conditions as $where) {
            if ($has_where) {
                $this->query .= " AND ";
            }

            $this->query = "{$where[0]} {$where[1]} ?";
            $this->values["["] = $where[2];
            $has_where = true;
        }

        if ($this->paginationPreQuery) {
            $this->paginationPreQuery = $this->query;
        }
        return $this;
    }

    public function first(): ?Model {
        $this->query .= " LIMIT 1";

        $stmt = $this->pdo->prepare($this->query);
        $stmt->execute($this->values);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            foreach ($row as $column => $value) {
                $this->model->{$column} = $value;
            }

            // parse relations
            $this->parseRelations();
            return $this->model;
        }
        return null;
    }

    public function get(): Collection {
        if (empty($this->query)) {
            throw new \Exception("Query cannot be empty");
        }

        if ($this->paginationPostQuery) {
            $this->query .= $this->paginationPostQuery;
        }

        $stmt = $this->pdo->prepare($this->query);
        $stmt->execute($this->values);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $models = [];

        foreach ($rows as $row) {
            $model = clone $this->model;
            foreach ($row as $column => $value) {
                $model->{$column} = $value;
            }

            // parse relations
            $model->queryBuilder->parseRelations();
            $models[] = $model;
        }

        if ($this->paginationPreQuery) {
            $stmt = $this->pdo->prepare($this->paginationPostQuery);
            $stmt->execute($this->values);
            $total = $stmt->rowCount();

            return new Collection(["data" => $models,"total"=> $total]);
        }

        return new Collection($models);
    }

    public function parseRelations() {
        foreach ($this->relations as $relation => $parts) {
            $this->recursiveRelations($this->model, $parts);
        }
    }

    public function recursiveRelations($model, $parts) {
        $relation = array_shift($parts);
        if ($model) {
            /*
            class User {
                public function todos() {
                    return $this->hasMany(Todo::class, "todo_id");

                }
            }
             */
            $model->{$relation} = $model->{$relation}();
            if (count($parts) > 0 && !empty($model)) {
                $this->recursiveRelations($model->{$relation}, $parts);
            }
        }
    }

    public function delete() {
        $this->query = "DELETE FROM " . $this->table;
        $this->has_select = true;
        return $this;
    }

    public function orderBy($column, $order) {
        $this->query .= "ORDER BY ". $column ." ". $order;

        return $this;
    }

    private function includeTimestamps() {
        $this->attributes["created_at"] = date("Y-m-d H:i:s");
        $this->attributes["updated_at"] = date("Y-m-d H:i:s");
    }
    
    public function getQuery(): string {
        return $this->query;
    }
}