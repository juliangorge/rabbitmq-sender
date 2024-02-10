<?php
namespace Juliangorge\RabbitMQSender\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Juliangorge\RabbitMQSender\RabbitMQSender;

class RabbitMQSenderPlugin extends AbstractPlugin 
{
    protected $sender;

    public function __construct($sm){
        $this->sender = new RabbitMQSender($sm);
    }

    public function send($queue, $message){
        return $this->sender->send($queue, $message);
    }

}
