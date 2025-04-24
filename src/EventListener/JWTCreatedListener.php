<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
        public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['id'] = $user->getId();        
        $payload['email'] = $user->getEmail();    
        $payload['roles'] = $user->getRoles();
        $payload['username'] = $user->getEmail();

        $event->setData($payload);
    }
}