<?php

namespace PTS\Transport\tests;

use PHPUnit\Framework\TestCase;
use PTS\Transport\File;
use PTS\Transport\Udp\UdpSocket;

class UdpSocketTest extends TestCase
{

    protected const HOST = '127.0.0.1';
    protected const PORT = 9999;

    protected static $serverSocket;
    protected static $file = __DIR__ . '/udp.txt';


    public static function setUpBeforeClass(): void
    {
        self::$serverSocket = self::createUdpServer();
    }

    public static function tearDownAfterClass(): void
    {
        self::shutdownUdpServer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink(self::$file);
    }

    protected static function createUdpServer()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($socket, self::HOST, self::PORT);

        // увеличиваем буффер сервера для чтения большого числа переданных пакетов
        ini_set('memory_limit', '32M');
        socket_set_option($socket, SOL_SOCKET, SO_RCVBUF, 5000000);
        socket_set_option($socket, SOL_SOCKET, SO_SNDBUF, 5000000);

        return $socket;
    }

    protected function acceptServerMessage(): int
    {
        $buffer = '';
        $message = '';

        $file = new File;
        $file->connect(self::$file, ['mode' => 'w+']);

        while (socket_recv(self::$serverSocket, $buffer, 8192, MSG_DONTWAIT)) {
            $message .= $buffer;
        }

        $file->write($message);
        $file->close();

        return mb_strlen($message, '8bit');
    }

    protected static function shutdownUdpServer(): void
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
        $transport = new UdpSocket;

        $transport->connect(self::HOST, ['port' => self::PORT]);
        static::assertTrue($transport->isConnected());

        $transport->getWriter()->setMaxChunkSize(8192);
        $bytes = $transport->write($message);
        static::assertSame($bytes, $countByte);

        $transport->close();
        static::assertFalse($transport->isConnected());

        $countByte = $this->acceptServerMessage();
        static::assertSame($bytes, $countByte);

        $log = file_get_contents(self::$file);
        static::assertSame($bytes, mb_strlen($log, '8bit'));
        static::assertSame($message, $log);
    }

    public function dataProvider(): array
    {
        $longMessage = '';
        for ($i = 0; $i < 300000; $i++) {
            $longMessage .= "message: $i" . PHP_EOL;
        }

        return [
            ['some message', 12],
            ['', 0],
            ["string #1 \r\nstring #2", 21],
            [$longMessage, mb_strlen($longMessage, '8bit')],
        ];
    }
}