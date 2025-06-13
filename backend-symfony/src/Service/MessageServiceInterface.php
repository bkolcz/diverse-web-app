<?php

namespace App\Service;

interface MessageServiceInterface {
    function save(string $message): string;
    function getAll(): array;
    function getOne(string $uuid): array;
    function sort(array $messageList, string $by, string $order): array;
}