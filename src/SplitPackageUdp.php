<?php
declare(strict_types=1);

namespace PTS\Transport;

class SplitPackageUdp extends UdpSocket
{

    public const CHUNK_MAGIC = "\x1e\x0f";

    protected $chunkSize = 8144;

    public function write(string $message, int $length = null): int
    {
        $packages = $this->splitToChunks($message);
        $bytes = 0;
        foreach ($packages as $package) {
            $bytes += (int)parent::write($package);
        }

        return $bytes;
    }

    /**
     * chunk format:
     * Chunked magic bytes - 2 bytes: 0x1e 0x0f
     * Message ID - 8 bytes: Must be the same for every chunk of this message. Identifying the whole message and is used to reassemble the chunks later
     * Sequence number - 1 byte: The sequence number of this chunk. Starting at 0 and always less than the sequence count
     * Sequence count - 1 byte: Total number of chunks this message has
     * Message chunk
     *
     * @param string $buffer
     *
     * @return array
     */
    protected function splitToChunks(string $buffer): array
    {
        $chunks = str_split($buffer, $this->chunkSize);
        $count = count($chunks);
        $id = $this->generateId();

        foreach ($chunks as $n => &$chunk) {
            $chunk = self::CHUNK_MAGIC . $id . pack('CC', $n, $count) . $chunk;
        }

        return $chunks;
    }

    protected function generateId(): string
    {
        return substr(md5(uniqid('', true), true), 0, 8);
    }
}
