<?php
declare(strict_types=1);

namespace PTS\Transport\Tcp;

use PTS\Transport\Socket;
use PTS\Transport\TransportInterface;
use RuntimeException;

class TcpSocket extends Socket
{
    protected $schema = 'tcp://';

    public function connect(string $address, array $options = []): TransportInterface
    {
        parent::connect($address, $options);

        if ($this->target === false) {
            throw new RuntimeException('can`t open socket - ' . $this->errorNumber . ': ' . $this->errorMessage);
        }

        return $this;
    }
}
