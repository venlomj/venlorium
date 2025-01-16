<?php

namespace Lib\Database;

class QB
{
    protected $pdo;
    protected $table;
    protected $fields;
    protected $wheres = [];
    protected $query; // Declare explicitly
    protected $model; // Declare explicitly

    public function __construct(Model $model = null)
    {
        $this->query = "";
        $this->pdo = Database::$pdo;
        $this->model = $model;
        if ($model) {
            $this->table = $model->table;
        }
    }


    public static function table($table)
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function select($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[] = [
            "type" => 'AND',
            "column" => $column,
            "operator" => $operator,
            "value" => $value
        ];
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->wheres[] = [
            "type" => 'OR',
            "column" => $column,
            "operator" => $operator,
            "value" => $value
        ];
        return $this;
    }

    public function get()
    {
        $sql = 'SELECT ' . $this->fields
            . ' FROM ' . $this->table;

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ';
            foreach ($this->wheres as $i => $where) {
                if ($i > 0) {
                    $sql .= $where['type'] . ' ';
                }
                $sql .= $where['column'] . ' '
                    . $where['operator'] . ' ?';
            }
        }
        $stmt = $this->pdo->prepare($sql);
        $bindedValues = array_column($this->wheres, 'value');
        $stmt->execute($bindedValues);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
