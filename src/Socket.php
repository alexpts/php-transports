<?php
declare(strict_types=1);

namespace PTS\Transport;

abstract class Socket implements TransportInterface
{

    /**
     * @var resource|null|false
     */
    protected $socket;

    /** @var string - udp:// tcp:// */
    protected $socketPrefix = '';

    protected $errorNumber = 0;
    protected $errorMessage = '';

    /**
     * @param string $address
     * @param int $port
     * @param array $options
     *
     * @return TransportInterface
     */
    public function connect(string $address, int $port = 0, array $options = []): TransportInterface
    {
        $timeout = (float)($options['timeout'] ??(float) ini_get('default_socket_timeout'));
        $url = $this->socketPrefix . $address;
        $this->socket = @fsockopen($url, $port, $this->errorNumber, $this->errorMessage, $timeout);

        return $this;
    }

    /**
     * @param string $buffer
     * @param int|null $length - записать число байт в сокет
     *
     * @return false|int число записанных байт или false
     */
    public function write(string $buffer, int $length = null): int
    {
        $length = $length ?? mb_strlen($buffer, '8bit');

        $written = 0;
        while ($written < $length) {
            $chunk = mb_substr($buffer, $written, $length, '8bit');
            $byteCount = fwrite($this->socket, $chunk, $length);

            if (!$byteCount) {
                break;
            }

            $written += $byteCount;
        }

        return $written;
    }

    public function isConnected(): bool
    {
        return is_resource($this->socket) && !feof($this->socket);
    }

    public function close(): void
    {
        $this->socket && fclose($this->socket);
        $this->socket = null;
    }
}
