<?php

use PHPUnit\Framework\TestCase;
use PTS\Transport\File;

class FileTransportTest extends TestCase
{
    protected string $file = __DIR__ . '/log.txt';

    public function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->file);
    }

    public function setUp(): void
    {
        parent::setUp();
        @touch($this->file);
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

        $transport->connect($this->file, ['mode' => 'w+']);
        static::assertTrue($transport->isConnected());

        $bytes = $transport->write($message);
        static::assertSame($countByte, $bytes);

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
            [str_repeat("very long string\n", 100), 1700],
        ];
    }

    public function testWriteToReadOnlyMode(): void
    {
        $transport = new File;
        $message = 'test';
        $expectedBytes = 0;

        $transport->connect($this->file, ['mode' => 'r']);
        static::assertTrue($transport->isConnected());

        $bytes = $transport->write($message);
        static::assertSame($expectedBytes, $bytes);

        $transport->close();
        static::assertFalse($transport->isConnected());
    }
}