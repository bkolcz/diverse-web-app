<?php

namespace App\Service;

class MessageFileService implements MessageServiceInterface
{
    public function save(string $message): string
    {
        return "";
    }
    public function getAll(): array
    {
        return [];
    }
    public function getOne(string $uuid): array
    {
        return [];
    }
    public function sort(array $messageList, string $by, string $order): array
    {
        return [];
    }
}
