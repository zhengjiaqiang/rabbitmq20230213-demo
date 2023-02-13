<?php
require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$vhost = 'sys';
$exc_name = 'delay_exc_pay';
$exc_type = 'x-delayed-message';
$queue_name = 'delay_queue_pay';
$routing_key = 'delay_route_pay';

//消息在丢弃之前的可存活时间
$ttl = 20000;
//创建链接
$connection = new AMQPStreamConnection('localhost', '5672', 'zjq', 'zjq19961030', $vhost);
//创建通道
$channel = $connection->channel();
//创建Exchange：通过exchange_declare()方法就可以创建一个exchange.
$channel->exchange_declare($exc_name, $exc_type, false, true, false);
//创建队列
$args = new AMQPTable(['x-delayed-type' => 'direct']);
$channel->queue_declare($queue_name, false, true, false, false, false, $args);
//绑定：通过queue_bind将Exchange和queue绑定到一块
$channel->queue_bind($queue_name, $exc_name, $routing_key);

$data = 'this is delay message';
//创建消息
$arr = ['delivery_mode' => AMQPMEssage::DELIVERY_MODE_PERSISTENT, 'application_headers' => new AMQPTable(['x-delay' => $ttl])];
$msg = new AMQPMessage($data, $arr);
//发送消息
$channel->basic_publish($msg, $exc_name, $routing_key);
echo " [x] Sent {$data}\n";
//关闭通道和连接;
$channel->close();
$connection->close();

