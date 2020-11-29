<?php
declare(strict_types=1);

namespace PTS\Transport\Udp;

use PTS\Transport\Socket;

class UdpSocket extends Socket
{

    protected bool $isConnected = false;
    protected string $schema = 'udp://';

    public function connect(string $address, array $options = []): static
    {
        $this->isConnected = true;
        return parent::connect($address, $options);
    }

    public function isConnected(): bool
    {
        return $this->isConnected && $this->target !== null;
    }

    public function close(): void
    {
        parent::close();
        $this->isConnected = false;
    }
}
