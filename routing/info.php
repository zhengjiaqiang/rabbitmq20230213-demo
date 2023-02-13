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
list($queue_name,,) = $channel->queue_declare('',false,false,true,false);
var_dump($queue_name);
$channel->queue_bind($queue_name,$exc_name,$routing_key);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$channel->basic_qos(null, 1, null);

$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();