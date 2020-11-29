<?php

namespace PTS\Transport;

interface WriterInterface
{

    /**
     * @param resource $target
     * @param string $buffer
     * @param int|null $length
     *
     * @return int size written bytes
     */
    public function write($target, string $buffer, int $length = null): int;

    public function setMaxChunkSize(int $size = 8192): void;
}
