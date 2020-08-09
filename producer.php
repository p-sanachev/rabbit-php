<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('messages', 'fanout', false, false, false);

$message['uri'] = 'https://google.com/';
$data = json_encode($message, JSON_THROW_ON_ERROR);
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'messages');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();
?>