<?php
require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$vhost = 'sys';
$exc_type = 'direct';

//消息在丢弃之前的可存活时间
$ttl = 20000;
//死信发送的交换机名字
$dead_exc_name = 'dead_exc_pay';
//死信的路由键
$dead_routing_key = 'dead_route_pay';
//死信队列名称
$dead_queue_name = 'dead_queue_pay';
//死信路由键
$dead_routing_key = 'dead_route_pay';
//创建链接
$connection = new AMQPStreamConnection('localhost', '5672', 'zjq', 'zjq19961030', $vhost);
//创建通道
$channel = $connection->channel();

//声明死信交换机队列
$channel->exchange_declare($dead_exc_name,$exc_type,false,false,false);
//绑定：通过queue_bind将Exchange和queue绑定到一块
$channel->queue_bind($dead_queue_name,$dead_exc_name,$dead_routing_key);
//echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
//只有consumer已经处理并确认了上一条message时queue才分派新的message给它
$channel->basic_qos(null, 1, null);
//消费消息
$channel->basic_consume($dead_queue_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();


