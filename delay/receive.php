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
$channel->queue_bind($queue_name,$exc_name,$routing_key);
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


