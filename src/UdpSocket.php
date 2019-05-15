<?php
declare(strict_types=1);

namespace PTS\Transport;

class UdpSocket extends Socket
{

    public const CHUNK_MAGIC_ID = "\x1e\x0f";

    /** @var bool */
    protected $isConnected = false;
    /** @var int */
    protected $chunkSize = 8144;
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

    public function write(string $message, int $length = null): int
    {
        $packages = $this->splitToChunks($message);
        $bytes = 0;
        foreach ($packages as $package) {
            $bytes += (int)parent::write($package, $length);
        }

        return $bytes;
    }

    protected function splitToChunks(string $buffer): array
    {
        $chunks = str_split($buffer, $this->chunkSize);
        $count = count($chunks);
        $id = substr(md5(uniqid('', true), true), 0, 8);

        foreach ($chunks as $n => &$chunk) {
            $chunk = self::CHUNK_MAGIC_ID . $id . pack('CC', $n, $count) . $chunk;
        }

        return $chunks;
    }
}
