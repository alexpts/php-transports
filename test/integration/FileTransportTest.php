<?php

use PHPUnit\Framework\TestCase;
use PTS\Transport\File;

class FileTransportTest extends TestCase
{

    protected $file = __DIR__ . '/temp//log.txt';

    public function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->file);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $message
     * @param int $countByte
     */
    public function testWrite(string $message, int $countByte): void
    {
        $transport = new File;

        $transport->connect($this->file, 0, ['mode' => 'w+']);
        static::assertTrue($transport->isConnected());

        $bytes = $transport->write($message);
        static::assertSame($bytes, $countByte);

        $transport->close();
        static::assertFalse($transport->isConnected());

        static::assertSame($message, file_get_contents($this->file));
    }

    public function dataProvider(): array
    {
        return [
            ['some message', 12],
            ['', 0],
            ["string #1 \r\nstring #2", 21],
        ];
    }
}