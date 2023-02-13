<?php
require_once  '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//创建连接
$connection = new AMQPStreamConnection('localhost', 5672, 'zjq', 'zjq19961030','sys');
//创建通道
$channel = $connection->channel();
//var_dump($connection);
//声明队列
$queue_name = 'hello';
$channel->queue_declare($queue_name,false,true,false,false);
//创建消息 (将队列设置为持久化之后，还需要将消息也设为可持久化的)
$msg = new AMQPMessage('hello world',['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
//往队列发送消息
$channel->basic_publish($msg,'',$queue_name);
echo " [x] Sent 'Hello World!'\n";
//关闭通道和连接;
$connection->close();
$channel->close();
