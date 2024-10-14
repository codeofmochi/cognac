<?php

namespace App\EventSubscriber;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessEventListener implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onLoginSuccess'],
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event) {
        /** @var User $user */
        $user = $event->getUser();
        // invalidate previous login links
        $user->setLastLoginAt(new DateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
