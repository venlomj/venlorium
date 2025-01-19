<?php

namespace Lib\Database;

use PDO;

class QueryBuilder {
    private PDO $pdo;
    private ?Model $model;
    private array $fields = [];
    private array $where = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $orderBy = [];
    private array $joins = [];
    private array $with = [];
    private array $eagerLoad = [];
    private ?string $table = null;

    // DML (Data Manipulation Language) constanten voor de querytypes
    const DML_TYPE_SELECT = 'SELECT';
    const DML_TYPE_INSERT = 'INSERT';
    const DML_TYPE_UPDATE = 'UPDATE';
    const DML_TYPE_DELETE = 'DELETE';

    // Constructor, maakt verbinding met de database en stelt de tabel in
    public function __construct(Model $model = null) {
        $this->pdo = Database::$pdo;  // Database connectie via PDO
        $this->model = $model;
        if ($model) {
            $this->table = $model->table;  // Stel de tabel in op basis van het model
        }
    }

    // Stel de tabelnaam in voor de query
    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    // Stel de velden in die geselecteerd moeten worden
    public function select(string $fields = "*"): self {
        $this->fields = explode(',', $fields);
        return $this;
    }

    // Voeg een WHERE clausule toe aan de query
    public function where(string $field, string $operator, mixed $value): self {
        $placeholder = ":" . str_replace(".", "_", $field) . count($this->bindings);
        $this->where[] = "$field $operator $placeholder";  // Voeg de voorwaarde toe aan de WHERE
        $this->bindings[$placeholder] = $value;  // Koppel de waarde aan de placeholder
        return $this;
    }

    // Voeg een relationele query toe voor 'eager loading'
    public function with($relations): self {
        $this->eagerLoad = is_string($relations) ? func_get_args() : $relations;
        return $this;
    }

    // Voeg een JOIN clausule toe aan de query
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";  // Voeg JOIN toe
        return $this;
    }

    // Voeg een ORDER BY clausule toe aan de query
    public function orderBy(string $field, string $direction = 'ASC'): self {
        $this->orderBy[] = "$field $direction";  // Voeg de volgorde toe
        return $this;
    }

    // Stel een limiet in voor het aantal resultaten
    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    // Stel een offset in voor het starten vanaf een bepaald record
    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }

    // Voer de SELECT query uit en retourneer de resultaten
    public function get(): array {
        $fields = !empty($this->fields) ? implode(', ', $this->fields) : '*';  // Velden om op te halen
        $sql = "SELECT $fields FROM {$this->table}";  // Begin van de query
    
        // Voeg JOIN clausules toe als die er zijn
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
    
        // Voeg WHERE clausule toe als die er is
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
    
        // Voeg ORDER BY clausule toe als die er is
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
    
        // Voeg LIMIT en OFFSET toe als die er zijn
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
    
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
    
        $stmt = $this->pdo->prepare($sql);  // Bereid de query voor
        $stmt->execute($this->bindings);  // Voer de query uit met de bindings
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Haal de resultaten op
    
        // Als er een model is en er eager loading is ingesteld, laad de relaties
        if ($this->model && !empty($this->eagerLoad)) {
            foreach ($results as &$result) {
                $model = clone $this->model;
                $model->fill($result);  // Vul het model met de data
                $model->load($this->eagerLoad);  // Laad de relaties
                $result = $model->toArray();  // Zet het model om naar een array
            }
        }
    
        return $results;
    }

    // Haal het eerste resultaat op
    public function first(): ?Model {
        $this->limit = 1;  // Beperk de resultaten tot 1
        $results = $this->get();
        
        if (!empty($results)) {
            return $this->model ? $this->model->fill($results[0]) : null;
        }
        
        return null;
    }

    // Voer de INSERT query uit om gegevens in te voegen
    public function insert(array $data): bool {
        $fields = array_keys($data);  // Haal de velden op
        $placeholders = array_map(fn($field) => ":$field", $fields);  // Maak placeholders voor de velden
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . 
               ") VALUES (" . implode(', ', $placeholders) . ")";  // Bouw de INSERT query op
        
        $stmt = $this->pdo->prepare($sql);  // Bereid de query voor
        return $stmt->execute($data);  // Voer de query uit met de data
    }

    // Voer de UPDATE query uit om gegevens te updaten
    public function update(int|string $id, array $data): bool {
        $fields = array_map(fn($field) => "$field = :$field", array_keys($data));  // Maak de UPDATE velden
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . 
               " WHERE id = :id";  // Bouw de UPDATE query op
        
        $stmt = $this->pdo->prepare($sql);  // Bereid de query voor
        return $stmt->execute([...$data, 'id' => $id]);  // Voer de query uit met de data en ID
    }

    // Voer de DELETE query uit om een record te verwijderen
    public function delete(int|string $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";  // Bouw de DELETE query op
        $stmt = $this->pdo->prepare($sql);  // Bereid de query voor
        return $stmt->execute(['id' => $id]);  // Voer de query uit met de ID
    }
}
