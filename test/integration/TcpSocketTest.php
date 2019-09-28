<?php

namespace PTS\Transport\tests;

use PHPUnit\Framework\TestCase;
use PTS\Transport\Tcp\TcpSocket;

class TcpSocketTest extends TestCase
{

    protected const HOST = '127.0.0.1';
    protected const PORT = 9999;

    protected static $serverSocket;
    protected static $file = __DIR__ . '/tcp.txt';


    public static function setUpBeforeClass(): void
    {
        self::$serverSocket = self::createTcpServer();
    }

    public static function tearDownAfterClass(): void
    {
        self::shutdownTcpServer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(self::$file);
    }

    protected static function createTcpServer()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($socket, self::HOST, self::PORT);
        socket_listen($socket, 10);

        return $socket;
    }

    protected function acceptServerMessage(): void
    {
        $clientSocket = socket_accept(self::$serverSocket);
        if ($clientSocket) {
            $buffer = socket_read($clientSocket, 65536);
            file_put_contents(self::$file, $buffer, FILE_APPEND);
            socket_close($clientSocket);
        }
    }

    protected static function shutdownTcpServer(): void
    {
        socket_close(self::$serverSocket);
        self::$serverSocket = null;
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $message
     * @param int $countByte
     */
    public function testWrite(string $message, int $countByte): void
    {
        $transport = new TcpSocket;

        $transport->connect(self::HOST, ['port' => self::PORT]);
        static::assertTrue($transport->isConnected());

        $bytes = $transport->write($message);
        static::assertSame($bytes, $countByte);

        $transport->close();
        static::assertFalse($transport->isConnected());

        $this->acceptServerMessage();
        static::assertSame($message, file_get_contents(self::$file));
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
}