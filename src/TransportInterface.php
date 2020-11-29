<?php

namespace PTS\Transport;

interface TransportInterface
{
    public function connect(string $address, array $options = []): static;

    public function write(string $buffer, int $length = null): int;

    public function close(): void;

    public function getWriter(): WriterInterface;
}
