<?php
require_once __DIR__ . '/../includes/db.php';

abstract class Model
{
    protected static $table;
    protected $attributes = [];
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected static $pdo;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        if (!self::$pdo) {
            self::$pdo = Database::getInstance();
        }
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || empty($this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->fillable) || empty($this->fillable)) {
            $this->attributes[$name] = $value;
        }
    }

    public static function all()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->fetch();
    }

    public function save()
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return $this->insert();
        }
        return $this->update();
    }

    protected function insert()
    {
        $columns = implode(', ', array_keys($this->attributes));
        $placeholders = ':' . implode(', :', array_keys($this->attributes));
        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";

        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute($this->attributes);

        if ($result) {
            $this->attributes[$this->primaryKey] = self::$pdo->lastInsertId();
            return true;
        }
        return false;
    }

    protected function update()
    {
        $set = [];
        foreach (array_keys($this->attributes) as $key) {
            if ($key !== $this->primaryKey) {
                $set[] = "$key = :$key";
            }
        }
        $set = implode(', ', $set);

        $sql = "UPDATE " . static::$table . " SET $set WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute($this->attributes);
    }

    public function delete()
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }
        $sql = "DELETE FROM " . static::$table . " WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$this->primaryKey => $this->attributes[$this->primaryKey]]);
    }

    public static function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $model = new static();
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE $column $operator ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public function toArray()
    {
        return $this->attributes;
    }
}
