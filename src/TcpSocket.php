<?php
declare(strict_types=1);

namespace PTS\Transport;

use RuntimeException;

class TcpSocket extends Socket
{
    protected $socketPrefix = 'tcp://';

    public function connect(string $address, int $port = 0, array $options = []): TransportInterface
    {
        parent::connect($address, $port, $options);

        if ($this->socket === false) {
            throw new RuntimeException('can`t open socket - ' . $this->errorNumber . ': ' . $this->errorMessage);
        }

        return $this;
    }

    public function write(string $buffer, int $length = null): int
    {
        return parent::write($buffer . "\0", $length);
    }
}
