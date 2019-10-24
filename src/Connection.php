<?php

namespace Mix\Redis\Subscriber;

use Mix\Redis\Subscriber\Exception\ConnectException;
use Mix\Redis\Subscriber\Exception\SendException;

/**
 * Class Connection
 * @package Mix\Redis\Subscriber
 */
class Connection
{

    /**
     * @var string
     */
    public $host = '';

    /**
     * @var int
     */
    public $port = 6379;

    /**
     * @var float
     */
    public $timeout = 0.0;

    /**
     * @var \Swoole\Coroutine\Client
     */
    protected $client;

    /**
     * EOF
     */
    const EOF = "\r\n";

    /**
     * Connection constructor.
     * @param string $host
     * @param int $port
     * @param float $timeout
     */
    public function __construct(string $host, int $port, float $timeout = 0.0)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
        $client        = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        $client->set([
            'open_eof_check' => true,
            'package_eof'    => static::EOF,
        ]);
        if (!$client->connect($host, $port, $timeout)) {
            throw new ConnectException(sprintf('Redis connect failed (host: %s, port: %s)', $host, $port));
        }
        $this->client = $client;
    }

    /**
     * Send
     * @param string $data
     * @return bool
     */
    public function send(string $data)
    {
        $len  = strlen($data);
        $size = $this->client->send($data);
        if ($size === false) {
            throw new SendException($this->client->errMsg, $this->client->errCode);
        }
        if ($len !== $size) {
            throw new SendException('The sending data is incomplete, it may be that the socket has been closed by the peer.');
        }
        return true;
    }

    /**
     * Recv
     * @return string|bool
     */
    public function recv()
    {
        return $this->client->recv(-1);
    }

    /**
     * Close
     * @return bool
     */
    public function close()
    {
        return $this->client->close();
    }

}
