<?php

namespace PTS\Transport;

class Writer
{

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
            $chunk = mb_substr($buffer, $written, $length, '8bit');
            $byteCount = fwrite($target, $chunk, $length);

            if (!$byteCount) {
                break;
            }

            $written += $byteCount;
        }

        return $written;
    }
}
