<?php

namespace oktaa\Database;

use Swoole\Runtime;

// Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL);


use PDO;
use PDOException;
use InvalidArgumentException;
use oktaa\Database\Interfaces\OrderByType;

abstract class Database
{
    protected $host;
    protected $db;
    protected $user;
    protected $password;
    protected $port;
    protected $dbh;
    protected $searchable = [];
    protected $params = [];
    protected string $sql;
    protected string $table;
    protected array $fillable = [];
    protected string $findableColumn = 'id';
    protected array $definition = ["id" => "INT PRIMARY KEY"];

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
            throw $e;
        }
    }

    public static function raw($sql, array $params = [])
    {
        $i = new static();
        $i->sql = $sql;
        $i->params = $params;
        return $i;
    }
    public static function getPdo()
    {
        $static = new static();
        return $static->dbh;
    }
    public static function find($value): object|false
    {

        $static = new static();
        $static->sql = "SELECT * FROM $static->table WHERE `$static->findableColumn` = ?";
        $stat = $static->dbh->prepare($static->sql);
        $static->params[] = $value;
        $stat->execute($static->params);
        $result = $stat->fetch(PDO::FETCH_ASSOC);
        return $result ? (object)$result : false;
    }
    public static function search($value)
    {
        $instance = new static();

        if (!is_array($instance->searchable)) {
            throw new InvalidArgumentException('Searchable must be an Array');
        }
        if (count($instance->searchable) < 1) {
            throw new InvalidArgumentException('There is no searchable column in table ' . $instance->getTableName() . "\n\n set searchable property! \n ex:\n`protected \$searchable = ['columnname'];`");
        }

        $conditions = [];
        $params = [];
        foreach ($instance->searchable as $column) {
            $conditions[] = "$column LIKE ?";
            $params[] = "%$value%";
        }

        $whereClause = implode(' OR ', $conditions);
        $instance->sql = "SELECT * FROM " . $instance->table . " WHERE $whereClause";


        try {
            $stmt = $instance->dbh->prepare($instance->sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw $th;
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
    public function get(): ?array
    {
        try {

            $stmt = $this->dbh->prepare($this->sql);
            $stmt->execute($this->params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function first(): object|false
    {
        try {
            $stmt = $this->dbh->prepare($this->sql);
            $stmt->execute($this->params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (object)$result : false;
        } catch (\Throwable $th) {
            echo $th->getMessage();
            throw $th;
        }
    }

    public function execute(): void
    {
        try {
            $stmt = $this->dbh->prepare($this->sql);
            $stmt->execute($this->params);
        } catch (\Throwable $th) {

            throw $th;
        }
    }
    /**
     * Inserts multiple rows into the database.
     *
     * @param array[] $data — Array of associative arrays to insert.
     *
     * Example usage:
     * $data = [
     *     ['column1' => 'value1', 'column2' => 'value2'],
     *     ['column1' => 'value3', 'column2' => 'value4'],
     * ];
     * self::insertMany($data);
     */
    public static function insertMany(array $data)
    {
        foreach ($data as $d) {
            self::insert($d)->run();
        }
    }
    /**
     * Update records in the database.
     *
     * @param string[] $data An associative array of columns and values to update.
     * @param string[] $conditions An associative array of conditions to match the records to update.
     *
     * @return Database
     *
     * Example usage:
     * $data = ['column' => 'value'];  // Columns to update
     * $conditions = ['id' => 1];      // Conditions to match the records
     * self::update($data, $conditions);
     */
    public static function update(array $data, array $conditions): Database
    {
        $instance = new static();

        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));

        $instance->sql = "UPDATE " . $instance->table . " SET $setClause WHERE $whereClause";

        $instance->params = array_merge(array_values($data), array_values($conditions));

        return $instance;
    }
    /**
     * Insert a row into the database.
     *
     * @param array $data — Array of associative array to insert.
     *
     * Example usage:
     * $data = ['column1' => 'value1', 'column2' => 'value2'];
     * 
     * self::insert($data);
     */
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
        // $instance->dbh->affectedrows();
    }
    public static function getTableName(): ?string
    {
        $ins = new static();
        return $ins->table;
    }


    public function run(bool $getaffectedrows = false)
    {
        $stmt = $this->dbh->prepare($this->sql);
        $stmt->execute($this->params);
        if ($getaffectedrows) {
            return $stmt->rowCount();
        }
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
    public function getSql(): ?string
    {
        return $this->sql ?? null;
    }
    public function OrderBy($column, OrderByType $type): Database
    {
        $this->sql .= " ORDER BY $column " . $type->value;
        return $this;
    }
    public function getParams()
    {
        return $this->params;
    }
}
