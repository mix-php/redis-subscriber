<?php

namespace Mix\Redis\Subscriber;

use Mix\Redis\Subscriber\Exception\ConnectException;

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
     * @return bool|int
     */
    public function send(string $data)
    {
        return $this->client->send($data);
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
