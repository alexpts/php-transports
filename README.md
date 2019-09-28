# Transports

[![Build Status](https://travis-ci.org/alexpts/php-transports.svg?branch=master)](https://travis-ci.org/alexpts/php-transports)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-transports/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-transports/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-transports/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-transports/?branch=master)

### Install

`composer require alexpts/php-transports`


Support `tcp`, `udp`, `file`, `unix socket` transports


```php
use PTS\Transport\File;
use PTS\Transport\Tcp\TcpSocket;
use PTS\Transport\Udp\UdpSocket;
use PTS\Transport\UnixSocket;

$udp = new UdpSocket;
$udp->connect('127.0.0.1', ['port' => 3000]);
$udp->write('some message');
$udp->close();

$unix = new UnixSocket;
$unix->connect(__DIR__ . '/controller.sock');
$unix->write('some command');
$unix->close();

$tcp = new TcpSocket;
$tcp->connect('127.0.0.1', ['port' => 3000]);
$tcp->write('some message');
$tcp->close();

$file = new File;
$file->connect(__DIR__ . '/log.txt', ['mode' => 'w+']);
$file->write('some message');
$file->close();
```
