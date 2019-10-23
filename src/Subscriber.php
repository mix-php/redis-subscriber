<?php

namespace Mix\Redis\Subscriber;

use Mix\Bean\BeanInjector;
use Mix\Redis\Subscriber\Exception\SubscribeException;

/**
 * Class Subscriber
 * @package Mix\Redis\Subscriber
 */
class Subscriber
{

    /**
     * 主机
     * @var string
     */
    public $host = '';

    /**
     * 端口
     * @var int
     */
    public $port = 6379;

    /**
     * 超时
     * @var float
     */
    public $timeout = 0.0;

    /**
     * 密码
     * @var string
     */
    public $password = '';

    /**
     * @var CommandInvoker
     */
    protected $commandInvoker;

    /**
     * Subscriber constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        BeanInjector::inject($this, $config);
        $connection           = new Connection($this->host, $this->port, $this->timeout);
        $this->commandInvoker = new CommandInvoker($connection);
        if ('' != (string)$this->password) {
            $this->commandInvoker->invoke("auth {$this->password}", 1);
        }
    }

    /**
     * Subscribe
     * @param string ...$channels
     * @return bool
     */
    public function subscribe(string ...$channels)
    {
        $result = $this->commandInvoker->invoke("subscribe " . join(' ', $channels), count($channels));
        foreach ($result as $value) {
            if ($value === false) {
                throw new SubscribeException('Subscribe failed');
            }
        }
        return true;
    }

    /**
     * Unsubscribe
     * @param string ...$channels
     * @return bool
     */
    public function unsubscribe(string ...$channels)
    {
        $result = $this->commandInvoker->invoke("unsubscribe " . join(' ', $channels), count($channels));
        foreach ($result as $value) {
            if ($value === false) {
                throw new SubscribeException('Unsubscribe failed');
            }
        }
        return true;
    }

    /**
     * Channel
     * @return \Swoole\Coroutine\Channel
     */
    public function channel()
    {
        return $this->commandInvoker->channel();
    }

    /**
     * Close
     * @return bool
     */
    public function close()
    {
        $this->commandInvoker->interrupt();
        return true;
    }

}
