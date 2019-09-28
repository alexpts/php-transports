<?php
declare(strict_types=1);

namespace PTS\Transport;

abstract class BaseTransport implements TransportInterface
{
    /** @var resource|null|false */
    protected $target;
    /** @var string */
    protected $schema = '';
    /** @var WriterInterface */
    protected $writer;

    public function __construct(WriterInterface $writer = null)
    {
        $this->writer = $writer ?? new Writer;
    }

    public function getWriter(): WriterInterface
    {
        return $this->writer;
    }

    /**
     * @param string $buffer
     * @param int|null $length - записать число байт в сокет
     *
     * @return false|int число записанных байт или false
     */
    public function write(string $buffer, int $length = null): int
    {
        return $this->writer->write($this->target, $buffer, $length);
    }

    public function isConnected(): bool
    {
        return is_resource($this->target) && !feof($this->target);
    }

    public function close(): void
    {
        $this->target && fclose($this->target);
        $this->target = null;
    }
}
