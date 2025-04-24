<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
        public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['id'] = $user->getId();          // utile en backend
        $payload['email'] = $user->getEmail();    // pour retrouver l'utilisateur
        $payload['roles'] = $user->getRoles();
        $payload['username'] = $user->getUsername(); // Ajoute ça à la place du unset
            // si tu veux du contrôle d'accès côté frontend

        // unset($payload['username']);              // on nettoie le champ par défaut

        $event->setData($payload);
    }
}