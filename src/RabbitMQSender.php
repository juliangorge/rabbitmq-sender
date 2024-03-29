<?php 
namespace Juliangorge\RabbitMQSender;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQSender
{
    protected $connection;
    protected $channel;

	public function __construct($config)
    {
        $rabbitmq = $config['rabbitmq-server'];

        $this->connection = new AMQPStreamConnection(
            $rabbitmq['host'],
            $rabbitmq['port'],
            $rabbitmq['user'],
            $rabbitmq['password']
        );
        $this->channel = $this->connection->channel();

		return $this;
	}

	public function checkHealth(){
        return 'pong';
    }

    public function send($queue, $message)
    {
        $this->channel->queue_declare($queue, false, false, false, false);

        if(is_array($message)){
            $message = json_encode($message);
        }

        $msg = new AMQPMessage($message);
        return $this->channel->basic_publish($msg, '', $queue);
    }

    public function __destruct()
    {   
        $this->channel->close();
        $this->connection->close();
    }

}