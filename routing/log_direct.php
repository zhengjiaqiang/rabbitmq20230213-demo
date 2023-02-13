<?php

require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//创建连接
$connection = new AMQPStreamConnection('localhost', '5672', 'zjq', 'zjq19961030', 'sys');
//创建通道
$channel = $connection->channel();
//交换机名称
$exc_name = 'direct_logs';
$routing_key = 'info';
$channel->exchange_declare($exc_name, 'direct', false, false, false);
//创建消息 (将队列设置为持久化之后，还需要将消息也设为可持久化的)
$data = 'this is '.$routing_key.' message';
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
$channel->basic_publish($msg, $exc_name,$routing_key);
echo " [x] Sent {$data}\n";
//关闭通道和连接;
$connection->close();
$channel->close();