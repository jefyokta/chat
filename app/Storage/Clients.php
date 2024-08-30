<?php
namespace oktaa\Storage;

use Swoole\Coroutine;

class ClientStorage
{
    private $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__."/../../storage/clients/clients.json";
    }

    public function saveClients(array $clients): void
    {
            Coroutine::writeFile($this->filePath, json_encode($clients));
    }

    public function loadClients(): array
    {
        $clients = [];
            $data = Coroutine::readFile($this->filePath);
            $clients = json_decode($data, true) ?? [];

        return $clients;
    }
}
