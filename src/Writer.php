<?php

namespace PTS\Transport;

class Writer implements WriterInterface
{

    /** @var int */
    protected $maxChunkSize = 0;

    public function setMaxChunkSize(int $size = 8192): void
    {
        $this->maxChunkSize = $size;
    }

    /**
     * @param resource $target
     * @param string $buffer
     * @param int|null $length - записать число байт в сокет
     *
     * @return false|int число записанных байт или false
     */
    public function write($target, string $buffer, int $length = null): int
    {
        $length = $length ?? mb_strlen($buffer, '8bit');

        $written = 0;
        while ($written < $length) {
            $size = $this->getWriteSize($length, $written);
            $chunk = mb_strcut($buffer, $written, $size, '8bit');
            $byteCount = fwrite($target, $chunk, $size);

            if (!$byteCount) {
                break;
            }

            $written += $byteCount;
        }

        return $written;
    }

    protected function getWriteSize(int $allSize, int $written): int
    {
        $maxSize = $this->maxChunkSize;
        if ($maxSize === 0) {
            return $allSize;
        }

        $needWrite = $allSize - $written;

        return $needWrite > $maxSize ? $maxSize : $needWrite;
    }
}
