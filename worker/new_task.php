<?php
require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//创建连接
$connection = new AMQPStreamConnection('localhost','5672','zjq','zjq19961030','sys');
//创建通道
$channel = $connection->channel();
//声明队列
$queue_name = 'task_queue';
$channel->queue_declare($queue_name,false,true,false,false);

for ($i=1;$i<=10;$i++){
    $data = "this is message {$i}";
    //使消息持久化
    $msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
    $channel->basic_publish($msg,'',$queue_name);
    echo " [x] Sent ", $data, "\n";
}
