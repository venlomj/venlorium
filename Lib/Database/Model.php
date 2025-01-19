<?php

namespace Lib\Database;

class Model extends \stdClass implements \JsonSerializable {
    // De primaire sleutel van het model (standaard 'id')
    protected string $primaryKey = "id";
    
    // De attributen die worden opgeslagen voor dit model
    protected array $attributes = [];
    
    // Attributen die niet moeten worden weergegeven bij serialisatie
    public array $hidden = [];
    
    // De naam van de tabel die bij dit model hoort
    public string $table = "";
    
    // Relaties tussen modellen
    protected array $relations = [];
    
    // Instantie van de QueryBuilder voor het uitvoeren van databasebewerkingen
    protected QueryBuilder $queryBuilder;

    // Constructor waarin de QueryBuilder wordt geïnitialiseerd
    public function __construct() 
    {
        $this->queryBuilder = new QueryBuilder($this);
    }

    // Magische setter voor het instellen van attributen
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    // Magische getter voor het ophalen van attributen of relaties
    public function __get($name) {
        // Eerst controleren of het een relatie is
        if (method_exists($this, $name)) {
            return $this->getRelationValue($name);
        }
        return $this->attributes[$name] ?? null;
    }

    // Haal de waarde van een relatie op
    protected function getRelationValue($name) {
        // Controleer of de relatie al geladen is
        if (!array_key_exists($name, $this->relations)) {
            // Laad de relatie als deze nog niet is geladen
            if (method_exists($this, $name)) {
                $this->relations[$name] = $this->$name();
            }
        }
        return $this->relations[$name];
    }

    // Vul de attributen van het model met een array
    public function fill(array $attributes): static {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }
    
    // Laad relaties van het model
    public function load($relations): self {
        $relations = is_string($relations) ? func_get_args() : $relations;
        
        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $this->relations[$relation] = $this->$relation();  // Laad relatiegegevens
            }
        }
        
        return $this;
    }

    // Haal de attributen van het model op
    public function getAttributes(): array {
        return $this->attributes;
    }

    // Hydrateer het model met gegevens uit een array
    public static function hydrate(array $data): self {
        $instance = new static();
        return $instance->fill($data);
    }

    // Selecteer gegevens uit de database
    public static function select(string $fields = "*"): QueryBuilder {
        $instance = new static();
        return $instance->queryBuilder->select($fields);
    }

    // Sla het model op in de database (insert of update)
    public function save(): bool {
        if (isset($this->attributes[$this->primaryKey])) {
            // Update als de primaire sleutel bestaat
            return $this->queryBuilder->update($this->attributes[$this->primaryKey], $this->attributes);
        } else {
            // Insert als de primaire sleutel niet bestaat
            return $this->queryBuilder->insert($this->attributes);
        }
    }

    // Verwijder een model uit de database op basis van het id
    public static function delete(int|string $id): bool {
        $instance = new static();
        return $instance->queryBuilder->delete($id);
    }

    // Relaties: hieronder definieer je verschillende relatie-methoden zoals:
    
    // Een-op-een relatie (HasOne)
    protected function hasOne(string $relatedClass, string $foreignKey = null): ?Model {
        $related = new $relatedClass();
        $foreignKey = $foreignKey ?? strtolower($this->class_basename($this)) . '_id';
        
        return $related::where($foreignKey, '=', $this->{$this->primaryKey})->first();
    }

    // Een-op-veel relatie (HasMany)
    protected function hasMany(string $relatedClass, string $foreignKey = null): array {
        $related = new $relatedClass();
        $foreignKey = $foreignKey ?? strtolower($this->class_basename($this)) . '_id';
        
        return $related::where($foreignKey, '=', $this->{$this->primaryKey})->get();
    }

    // Veel-op-een relatie (BelongsTo)
    protected function belongsTo(string $relatedClass, string $foreignKey = null): ?Model {
        $related = new $relatedClass();
        $foreignKey = $foreignKey ?? strtolower($this->class_basename($related)) . '_id';
        
        return $related::where($related->primaryKey, '=', $this->$foreignKey)->first();
    }

    // Veel-op-veel relatie via een tussenmodel (HasManyThrough)
    protected function hasManyThrough(string $relatedClass, string $throughClass, 
        string $firstKey = null, string $secondKey = null): array {
        $related = new $relatedClass();
        $through = new $throughClass();
        
        $firstKey = $firstKey ?? strtolower($this->class_basename($this)) . '_id';
        $secondKey = $secondKey ?? strtolower($this->class_basename($through)) . '_id';
        
        return $this->queryBuilder
            ->table($related->table)
            ->select("*")
            ->join($through->table, 
                   "$through->table.$secondKey", 
                   "$related->table.id", '=')  // De juiste tabelreferenties corrigeren
            ->where("$through->table.$firstKey", '=', $this->{$this->primaryKey})
            ->get();
    }

    // Helperfunctie om de naam van een klasse te verkrijgen zonder namespace
    protected function class_basename($class): string {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    // Statische methoden voor databasequery's
    
    public static function query(): QueryBuilder {
        return (new static)->queryBuilder;
    }

    // Zoek een model op op basis van het id
    public static function find(int|string $id): ?self {
        return (new static)->queryBuilder->where('id', '=', $id)->first();
    }

    // Haal alle records op van de tabel
    public static function all(): array {
        return (new static)->queryBuilder->get();
    }

    // Maak een waar-query voor een specifiek veld
    public static function where(string $field, string $operator, mixed $value): QueryBuilder {
        return (new static)->queryBuilder->where($field, $operator, $value);
    }

    // Laad relaties samen met de query
    public static function with(array|string $relations): QueryBuilder {
        return (new static)->queryBuilder->with($relations);
    }

    // JSON-serialisatie: Excludeer de verborgen attributen en voeg relaties toe
    public function jsonSerialize(): mixed {
        $data = array_diff_key($this->attributes, array_flip($this->hidden));
        
        foreach ($this->relations as $key => $value) {
            $data[$key] = $value;
        }
        
        return $data;
    }

    // Zet het model om naar een array
    public function toArray(): array {
        return $this->jsonSerialize();
    }
}
