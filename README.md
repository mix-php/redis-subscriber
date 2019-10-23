## Mix Redis Subscriber

一个使用 Swoole Socket 直接连接 Redis 服务器，不依赖 phpredis 扩展的订阅器，该订阅器有如下优点：

- 平滑修改：可随时增加、取消订阅通道，可实现无缝切换。
- 跨协程安全关闭：可在任意时刻关闭订阅。
- 通道获取消息：该库封装风格参考 golang 语言 [go-redis](https://github.com/go-redis/redis) 库封装，通过 channel 获取订阅的消息。

## 环境依赖 (Require)

* PHP >= 7.0
* Swoole >= 4.0

## 使用 (Usage)

安装：

```
composer require mix/redis-subscriber
```

代码：

- 连接失败会抛出异常 `ConnectException`
- 订阅失败会抛出异常 `SubscribeException`

```
$sub = new \Mix\Redis\Subscriber\Subscriber([
    'host'     => '192.168.198.1',
    'port'     => 6379,
    'timeout'  => 5,
    'password' => '',
]);
$sub->subscribe('aa', 'bb');
$chan = $sub->channel();
while (true) {
    $data = $chan->pop();
    if (empty($data)) {
        break;
    }
    var_dump($data);
}
```

接收到订阅消息：

```
object(Mix\Redis\Subscriber\Message)#8 (2) {
  ["channel"]=>
  string(2) "bb"
  ["payload"]=>
  string(4) "1111"
}
```

全部方法：

|  方法  |  描述  |
| --- | --- |
|  subscribe(string ...$channels) : bool  |  增加订阅  |
|  unsubscribe(string ...$channels) : bool  |  取消订阅  |
|  channel() : Channel  |  获取消息通道  |
|  close() : bool  |  关闭订阅  |

## License

Apache License Version 2.0, http://www.apache.org/licenses/
