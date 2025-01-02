<?php

namespace App\Service\Mail;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailService
{
    private Environment $twig;

    public function __construct(
        #[Autowire('%email_from%')] private readonly string $senderEmail,
        private readonly MailerInterface                      $mailer,
        Environment                                           $twig,
    ) {
        $this->twig = $twig;
    }


    final public function sendMailNewUser(User $user): void
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to($user->getEmail())
            ->subject("Bienvenue sur Reserving !")
            ->html($this->twig->render('email/welcome.html.twig', [
                'user' => $user,
            ]));
        $this->mailer->send($email);
    }
}

