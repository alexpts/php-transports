<?php
declare(strict_types=1);

namespace PTS\Transport;

class File extends Socket
{

    public function connect(string $address, int $port = 0, array $options = []): TransportInterface
    {
        $mode = $options['mode'] ?? 'a';
        $include_path = $options['use_include_path'] ?? false;
        $context = $options['context'] ?? null;

        $this->socket = fopen($address, $mode, $include_path, $context);

        return $this;
    }
}
