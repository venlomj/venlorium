<?php

namespace Lib\Database;

use PDO;

class QueryBuilder {
    private PDO $pdo;
    private ?Model $model;
    private string $table;
    private array $fields = [];
    private array $where = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private ?string $orderBy = null;
    private ?string $dmlType = null;
    private array $joins = [];
    private array $data = [];


    protected ?string $query = null;

    const DML_TYPE_SELECT = 'SELECT';
    const DML_TYPE_INSERT = 'INSERT';
    const DML_TYPE_UPDATE = 'UPDATE';
    const DML_TYPE_DELETE = 'DELETE';

    public function __construct(Model $model = null)
    {
        $this->query = "";
        $this->pdo = Database::$pdo;
        $this->model = $model;
        if ($model) {
            $this->table = $model->table;
        }
    }

    public function getDmlType(): ?string
    {
        return $this->dmlType ?? self::DML_TYPE_SELECT;
    }
    
    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function select(string $fields): static
    {
        $this->dmlType = self::DML_TYPE_SELECT;
        $this->fields = !empty($fields) ? $fields : ["*"];
        return $this;
    }

    public function where(string $field, string $operator, string|int|float|null $value): static
    {
        $placeholder = ":" . str_replace(".","_", $field) . count($this->bindings);
        $this->where[] = "$field $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): static
    {
        $this->orderBy[] = "$field $direction";
        return $this;
    }

    public function paginate(int $perPage, int $currentPage): array
    {
        $this->limit = $perPage;
        $this->offset = ($currentPage -1) * $perPage;

        $results = $this->get();
        $total = $this->count();

        return [
            "data" => $results,
            "total"=> $total,
            "per_page"=> $perPage,
            "current_page"=> $currentPage,
            "last_page" => ceil($total / $perPage),
        ];
    }

    public function join(string $table, string $firstColumn, string $secondColumn, string $operator, string $joinType = 'INNER'): static
    {
        $this->joins[] = "$joinType JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }

    public function get(): array
    {
        $this->dmlType = self::DML_TYPE_SELECT;

        // Ensure that fields are set before using them in the query
        if (empty($this->fields)) {
            $this->fields = ['*']; // Default to selecting all columns if fields are empty
        }

        $sql = "SELECT " . implode(', ', $this->fields) . " FROM $this->table";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        if ($this->orderBy) {
            $sql .= " ORDER BY $this->orderBy";
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT $this->limit";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET $this->offset";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM $this->table";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    public function find(int|string $id, string $primaryKey = 'id'): ?array
{
    $this->dmlType = self::DML_TYPE_SELECT;

    // Default fields to '*' if not explicitly set
    $fields = $this->fields ?: ['*'];

    $sql = "SELECT " . implode(', ', $fields) . " FROM $this->table WHERE $primaryKey = :id LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}


    public function insert(array $data): bool
    {
        $this->dmlType = self::DML_TYPE_INSERT;
        $this->data = $data;

        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $this->table ($fields) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function update(int|string $id, array $data): bool
    {
        $this->dmlType = self::DML_TYPE_UPDATE;
        $this->data = $data;

        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
        }

        $sql = "UPDATE $this->table SET " . implode(', ', $updates) . " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete(int|string $id): bool
    {
        $this->dmlType = self::DML_TYPE_DELETE;

        $sql = "DELETE FROM $this->table WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}
