<?php
require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$vhost = 'sys';
$exc_name = 'exc_pay';
$exc_type = 'direct';
$queue_name = 'queue_pay';
$routing_key = 'route_pay';

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
//创建Exchange：通过exchange_declare()方法就可以创建一个exchange.
$channel->exchange_declare($exc_name, $exc_type, false, false, false);
//创建队列
$args = new AMQPTable([
    'x-message-ttl' => $ttl,
    'x-dead-letter-exchange' => $dead_exc_name,
    'x-dead-letter-routing-key' => $dead_routing_key
]);
$channel->queue_declare($queue_name, false, true, false, false, false, $args);
//绑定：通过queue_bind将Exchange和queue绑定到一块
$channel->queue_bind($queue_name,$exc_name,$routing_key);

//声明死信交换机队列
$channel->exchange_declare($dead_exc_name,$exc_type,false,false,false);
$channel->queue_declare($dead_queue_name, false, true, false, false);
$channel->queue_bind($dead_queue_name,$dead_exc_name,$dead_routing_key);
$data = 'this is dead message';
//创建消息
$msg = new AMQPMessage($data,['delivery_mode'=>AMQPMEssage::DELIVERY_MODE_PERSISTENT]);
//发送消息
$channel->basic_publish($msg,$exc_name,$routing_key);
echo " [x] Sent {$data}\n";
//关闭通道和连接;
$channel->close();
$connection->close();

