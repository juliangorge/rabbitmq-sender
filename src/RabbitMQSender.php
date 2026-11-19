<?php 
namespace Juliangorge\RabbitMQSender;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
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
        $this->ensureQueue($queue);

        if(is_array($message)){
            $message = json_encode($message);
        }

        // Persistente: sobrevive a un reinicio del broker (en una cola durable).
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        return $this->channel->basic_publish($msg, '', $queue);
    }

    /**
     * Si la cola ya existe la usa tal cual (durable o no); si no existe, la crea durable.
     *
     * Declarar con otros atributos que los de una cola existente hace que RabbitMQ
     * rechace el declare (PRECONDITION_FAILED). El declare pasivo sólo verifica que
     * exista, así este código convive con las colas no durables viejas mientras se
     * migra, y crea durables las que falten (RabbitMQ 4.x bloquea las no durables).
     */
    protected function ensureQueue($queue)
    {
        try {
            $this->channel->queue_declare($queue, true);
        } catch (AMQPProtocolChannelException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
            // El 404 del declare pasivo cierra el canal: se abre otro para crearla.
            $this->channel = $this->connection->channel();
            $this->channel->queue_declare($queue, false, true, false, false);
        }
    }

    public function __destruct()
    {   
        $this->channel->close();
        $this->connection->close();
    }

}