<?php
declare(strict_types=1);

namespace PTS\Transport;

abstract class BaseTransport implements TransportInterface
{
    /** @var resource|null|false */
    protected $target;
    protected string $schema = '';
    protected WriterInterface $writer;

    public function __construct(WriterInterface $writer = null)
    {
        $this->writer = $writer ?? new Writer;
    }

    public function getWriter(): WriterInterface
    {
        return $this->writer;
    }

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
