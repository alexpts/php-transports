<?php

namespace PTS\Transport;

interface WriterInterface
{

    /**
     * @param resource $target
     * @param string $buffer
     * @param int|null $length - записать число байт в сокет
     *
     * @return false|int число записанных байт или false
     */
    public function write($target, string $buffer, int $length = null): int;
}
