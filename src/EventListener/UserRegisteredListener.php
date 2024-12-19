<?php

namespace App\EventListener;

use App\Event\UserRegisteredEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserRegisteredListener
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer, private readonly string $emailFrom)
    {
        $this->mailer = $mailer;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();

        $email = (new Email())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Bienvenue sur notre site !')
            ->text('Merci de vous être inscrit !');

        $this->mailer->send($email);
    }
} 