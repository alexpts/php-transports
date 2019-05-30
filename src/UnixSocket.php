<?php
declare(strict_types=1);

namespace PTS\Transport;

class UnixSocket extends TcpSocket
{
    protected $schema = 'unix://';
}
