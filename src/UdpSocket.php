<?php
declare(strict_types=1);

namespace PTS\Transport;

class UdpSocket extends Socket
{

    /** @var bool */
    protected $isConnected = false;
    /** @var string  */
    protected $socketPrefix = 'udp://';

    public function connect(string $address, int $port = 0, array $options = []): TransportInterface
    {
        $this->isConnected = true;
        return parent::connect($address, $port, $options);
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function close(): void
    {
        parent::close();
        $this->isConnected = false;
    }
}
