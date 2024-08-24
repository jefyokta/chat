<?php

namespace oktaa\Database;

use PDO;
use PDOException;

abstract class Database
{
    // Properti kelas
    protected $host;
    protected $db;
    protected $user;
    protected $password;
    protected $port;
    protected $dbh;
    protected $params = [];
    protected string $sql;
    protected string $table;
    protected array $fillable = [];
    protected array $definition = ["id" => "INT PRIMARY KEY", "name" => "VARCHAR"];

    public function __construct()
    {
        $this->host = config('db.host');
        $this->db = config('db.name');
        $this->user = config('db.user');
        $this->password = config('db.password');
        $this->port = config('db.port');

        try {
            $this->dbh = new PDO(config('db.connection') . ":host=$this->host;port=$this->port;dbname=$this->db", $this->user, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
            exit;
        }
    }

    public static function select(string $columns): Database
    {
        $instance = new static();
        $instance->sql = "SELECT $columns FROM " . $instance->table;
        return $instance;
    }

    public function where(string $column, string $compare, $value): Database
    {
        $this->sql .= " WHERE $column $compare ?";
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $compare, $value): Database
    {
        $this->sql .= " AND $column $compare ?";
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $compare, $value): Database
    {
        $this->sql .= " OR $column $compare ?";
        $this->params[] = $value;
        return $this;
    }
    public function get(): array
    {
        $stmt = $this->dbh->prepare($this->sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function first()
    {
        $stmt = $this->dbh->prepare($this->sql);
        $stmt->execute($this->params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?? [];
    }

    public function execute(): void
    {
        $stmt = $this->dbh->prepare($this->sql);
        $stmt->execute($this->params);
    }

    public static function update(array $data, array $conditions): Database
    {
        $instance = new static();

        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));

        $instance->sql = "UPDATE " . $instance->table . " SET $setClause WHERE $whereClause";

        $instance->params = array_merge(array_values($data), array_values($conditions));

        return $instance;
    }

    public static function insert(array $data): Database
    {
        $instance = new static();
        $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));

        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $instance->sql = "INSERT INTO " . $instance->table . " ($columns) VALUES ($placeholders)";
        $instance->params = array_values($data);

        return $instance;
    }



    public static function delete(): Database
    {
        $instance = new static();
        $instance->sql = "DELETE FROM " . $instance->table;
        return $instance;
    }

    public static function migrate(): void
    {
        $instance = new static();
        $instance->sql = "DROP TABLE IF EXISTS " . $instance->table;
        $instance->run();
        $instance->sql = "CREATE TABLE IF NOT EXISTS " . $instance->table . " (" . $instance->formatDefinition() . ")";
        $instance->run();
    }

    public function run(): void
    {
        $stmt = $this->dbh->prepare($this->sql);
        $stmt->execute($this->params);
    }
    public function join(string $table, string $clause): Database
    {
        $this->sql .= " INNER JOIN $table ON $clause";
        return $this;
    }

    protected function formatDefinition(): string
    {
        return implode(', ', array_map(
            fn($col, $type) => "$col $type",
            array_keys($this->definition),
            $this->definition
        ));
    }
    public static function selectDistinct($columns)
    {
        $instance = new static();
        $instance->sql = "SELECT DISTINCT $columns FROM " . $instance->table;
        return $instance;
    }
}
