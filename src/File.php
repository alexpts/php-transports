<?php
declare(strict_types=1);

namespace PTS\Transport;

class File extends BaseTransport
{

    public function connect(string $address, array $options = []): TransportInterface
    {
        $mode = $options['mode'] ?? 'a';
        $include_path = $options['use_include_path'] ?? false;
        $context = $options['context'] ?? null;

        $params = [$address, $mode, $include_path];
        $context && array_push($params, $context);

        $this->target = fopen(...$params);

        return $this;
    }
}
