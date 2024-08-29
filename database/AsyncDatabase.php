<?php


namespace oktaa\Database;

use PDO;
use PDOException;
use InvalidArgumentException;
use oktaa\Database\Interfaces\OrderByType;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

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

        // Coroutine::create(function () {
        // try {
        //     $this->dbh = new PDO(config('db.connection') . ":host=$this->host;port=$this->port;dbname=$this->db", $this->user, $this->password);
        //     $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch (PDOException $e) {
        //     throw $e;
        // }
        // });
        $this->connect();
    }
    private function connect()
    {

        // $this->dbh = new MySQL();
        // $this->dbh->connect([
        //     "host" => $this->host,
        //     "port" => $this->port,
        //     "user" => $this->user,
        //     "password" => $this->password,
        //     "database" => $this->db
        // ]);
        try {
            $this->dbh = new PDO(
                config('db.connection') . ":host=$this->host;port=$this->port;dbname=$this->db",
                $this->user,
                $this->password
            );
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log error atau tampilkan pesan error
            echo "Connection failed: " . $e->getMessage() . "\n";
            exit;
        }
    }

    public static function find($value): array
    {
        $instance = new static();
        return Coroutine::create(function () use ($instance, $value) {
            $instance->sql = "SELECT * FROM $instance->table WHERE `$instance->findableColumn` = ?";
            $stat = $instance->dbh->prepare($instance->sql);
            $instance->params[] = $value;
            $stat->execute($instance->params);
            $result = $stat->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        });
    }

    public static function search($value)
    {
        $instance = new static();
        return Coroutine::create(function () use ($instance, $value) {
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
        });
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
        $channel = new Channel(1);

        Coroutine::create(function () use ($channel) {
            try {
                // $this->connect();
                $stmt =  $this->dbh->prepare($this->sql);
                $stmt->execute($this->params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $channel->push($result);
            } catch (\Throwable $th) {
                // throw $th;
                $channel->push([]);
            }
        });
        return $channel->pop();
    }

    public function first()
    {
        return Coroutine::create(function () {
            $this->connect();
            Coroutine::wait(1);
            $stmt =  $this->dbh->prepare($this->sql);
            $stmt->execute($this->params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?? [];
        });
    }

    public function execute(): void
    {
        Coroutine::create(function () {
            try {
                $stmt = $this->dbh->prepare($this->sql);
                $stmt->execute($this->params);
            } catch (\Throwable $th) {
                throw $th;
            }
        });
    }

    public static function insertMany(array $data)
    {
        foreach ($data as $d) {
            self::insert($d)->run();
        }
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

    public static function getTableName(): ?string
    {
        $ins = new static();
        return $ins->table;
    }



    public function run(bool $getaffectedrows = false)
    {
        return Coroutine::create(function () use ($getaffectedrows) {
            $stmt = $this->dbh->prepare($this->sql);
            $stmt->execute($this->params);
            if ($getaffectedrows) {
                return $stmt->rowCount();
            }
        });
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
        return $this->sql;
    }
    public function OrderBy($column, OrderByType $type): Database
    {
        $this->sql .= " ORDER BY $column " . $type->value;
        return $this;
    }
}
