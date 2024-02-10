<?php
namespace Juliangorge\RabbitMQSender\Service;

class RabbitMQSender
{
    protected $sender;

    public function __construct($sm){
        $this->sender = new \Juliangorge\RabbitMQSender\RabbitMQSender($sm);
    }

    public function send($queue, $message){
        return $this->sender->send($queue, $message);
    }

}