<?php 

namespace App\Controller\Front\Security;

use App\Entity\User;
use App\Form\AppRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Event\UserRegisteredEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Email;

#[Route('/register', name: 'app_register_')]
class RegisterController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EventDispatcherInterface $eventDispatcher): Response
    {
        $user = new User();
        $form = $this->createForm(AppRegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $this->manager->persist($user);
            $this->manager->flush();

            $eventDispatcher->dispatch(new UserRegisteredEvent($user), UserRegisteredEvent::NAME);

            $this->addFlash('success', 'Votre compte a bien été créé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/test-envoi-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = new Email();
        $email->from('contact@asmanissieux.fr');
        $email->to('kademkamilg@gmail.com');
        $email->subject('Bienvenue sur notre site !');
        $email->text('Merci de vous être inscrit !');

        $mailer->send($email);

        return $this->redirectToRoute('app_home');
    }

}
