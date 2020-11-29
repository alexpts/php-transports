<?php
declare(strict_types=1);

namespace PTS\Transport\Tcp;

use PTS\Transport\Socket;

class TcpSocket extends Socket
{
    protected string $schema = 'tcp://';
}
