<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListener implements EventSubscriberInterface
{
    public function checkRequest(GetResponseEvent $event)
    {
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'checkRequest',
        ];
    }
}
