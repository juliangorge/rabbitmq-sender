<?php

declare(strict_types=1);

namespace Juliangorge\RabbitMQSender;

return [
    'service_manager' => [
        'factories' => [
            Service\RabbitMQSender::class => Service\Factory\ServiceFactory::class
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            Plugin\RabbitMQSenderPlugin::class => Plugin\Factory\PluginFactory::class,
        ],
        'aliases' => [
            'rabbitmq-sender' => Plugin\RabbitMQSenderPlugin::class
        ]
    ],
];