<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('messages', 'fanout', false, false, false);

[$queue_name, ,] = $channel->queue_declare("messages", false, false, false, false);

$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    $message = json_decode($msg->body, true);
    $url = $message['uri'];
    echo file_get_contents($url);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
