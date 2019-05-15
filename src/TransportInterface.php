<?php

namespace PTS\Transport;

interface TransportInterface
{
    public function connect(string $address, int $port = 0, array $options = []): TransportInterface;

    public function write(string $message, int $length = null): int;

    public function close(): void;
}
