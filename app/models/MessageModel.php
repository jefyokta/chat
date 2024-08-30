<?php

namespace oktaa\model;

use oktaa\Database\Database as Database;
use oktaa\Database\Interfaces\OrderByType;

class MessageModel extends Database
{
    protected string $table = 'messages';
    protected array $definition = [
        "id" => "VARCHAR(255) PRIMARY KEY",
        "message" => "TEXT",
        "`from`" => "INT NULL",
        "`to`" => "INT NULL",
        "created_at" => "DATETIME DEFAULT CURRENT_TIMESTAMP"
    ];
    protected array $fillable = ["message", "from", "to"];
    public $name = 'messages';


    public static function getOurMessage($myid, $theirid)
    {
        return  self::select('*')->where('messages.from', '=', $myid)->orWhere('messages.from', '=', $theirid)->orWhere('messages.to', '=', $myid)->orWhere('messages.to', '=', $theirid)->OrderBy('messages.created_at', OrderByType::ASC)->get();
    }
}
