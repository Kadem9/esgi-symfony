<?php

namespace App\EventSubscriber;


use App\Event\UserRegisteredEvent;
use App\Service\Mail\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class UserRegisteredListener implements EventSubscriberInterface
{
    public function __construct(
        private MailService $mailService
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {

        $user = $event->getUser();

        $this->mailService->sendMailNewUser($user);

    }
}
