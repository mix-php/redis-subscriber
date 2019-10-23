<?php

require __DIR__ . '/../vendor/autoload.php';

$scheduler = new \Swoole\Coroutine\Scheduler;
$scheduler->add(function () {

    $sub = new \Mix\Redis\Subscriber\Subscriber([
        'host'     => '192.168.198.1',
        'port'     => 6379,
        'timeout'  => 5,
        'password' => '',
    ]);
    $ret = $sub->subscribe('aa', 'bb');
    var_dump($ret);
    if ($ret) {

        \Swoole\Timer::after(10000, function () use ($sub) {
            $sub->close();
        });

        $chan = $sub->channel();
        while (true) {
            $data = $chan->pop();
            if (empty($data)) {
                break;
            }
            var_dump($data);
        }
    }

});
$scheduler->start();
