<?php

namespace PTS\Transport\tests;

use PHPUnit\Framework\TestCase;
use PTS\Transport\UnixSocket;

class UnixSocketTest extends TestCase
{

    protected const UNIX_SOCKET = __DIR__ . '/unix.sock';

    protected static $serverSocket;
    protected static $file = __DIR__ . '/unix.txt';


    public static function setUpBeforeClass(): void
    {
        self::$serverSocket = self::createUnixServer();
    }

    public static function tearDownAfterClass(): void
    {
        self::shutdownUnixServer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(self::$file);
    }

    protected static function createUnixServer()
    {
        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        socket_bind($socket, self::UNIX_SOCKET);
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

    protected static function shutdownUnixServer(): void
    {
        socket_close(self::$serverSocket);
        self::$serverSocket = null;
        @unlink(self::UNIX_SOCKET);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $message
     * @param int $countByte
     */
    public function testWrite(string $message, int $countByte): void
    {
        $transport = new UnixSocket;

        $transport->connect(self::UNIX_SOCKET);
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