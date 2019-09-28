<?php
declare(strict_types=1);

namespace PTS\Transport\Udp;

use PTS\Transport\Socket;
use PTS\Transport\TransportInterface;

class UdpSocket extends Socket
{

    /** @var bool */
    protected $isConnected = false;
    /** @var string  */
    protected $schema = 'udp://';

    public function connect(string $address, array $options = []): TransportInterface
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
