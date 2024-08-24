<?php
namespace oktaa\model\MessageModel;
use oktaa\Database\Database;
class MessageModel extends Database
{
    protected string $table = 'messages';
    protected array $definition = [
        "id" => "INT PRIMARY KEY AUTO_INCREMENT",
        "message" => "TEXT",
        "`from`" => "INT NULL",
        "`to`" => "INT NULL",
        "created_at" => "DATETIME DEFAULT CURRENT_TIMESTAMP"
    ];
    protected array $fillable = ["message", "from", "to"];
    public $name = 'messages';
}
