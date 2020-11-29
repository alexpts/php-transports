<?php
declare(strict_types=1);

namespace PTS\Transport;

use PTS\Transport\Tcp\TcpSocket;

class UnixSocket extends TcpSocket
{
    protected string $schema = 'unix://';
}
