<?php
require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//创建连接
$connection = new AMQPStreamConnection('localhost', 5672, 'zjq', 'zjq19961030','sys');
//创建通道
$channel = $connection->channel();
//声明队列
$queue_name = 'hello';
$channel->queue_declare($queue_name,false,true,false,false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

//消费消息
$channel->basic_consume('hello', '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
