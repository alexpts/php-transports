<?php
declare(strict_types=1);

namespace PTS\Transport;

class Socket extends BaseTransport
{

    protected int $errorNumber = 0;
    protected string $errorMessage = '';

    public function connect(string $address, array $options = []): static
    {
        if ($this->target === null) {
            $timeout = (float)($options['timeout'] ??(float) ini_get('default_socket_timeout'));
            $port = $options['port'] ?? 0;
            $url = $this->schema . $address;
            $this->target = fsockopen($url, $port, $this->errorNumber, $this->errorMessage, $timeout);
        }

        return $this;
    }
}
