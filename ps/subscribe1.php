<?php
require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//创建连接
$connection = new AMQPStreamConnection('localhost','5672','zjq','zjq19961030','sys');
//创建通道
$channel = $connection->channel();
list($queue_name, ,) = $channel->queue_declare("");
//交换机名称
$exc_name ='exch';
$channel->queue_bind($queue_name,$exc_name);
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ', $msg->body, "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();