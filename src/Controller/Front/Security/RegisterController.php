<?php 

namespace App\Controller\Front\Security;

use App\Entity\User;
use App\Form\AppRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Event\UserRegisteredEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/register', name: 'app_register_')]
class RegisterController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $manager, private readonly EventDispatcherInterface $dispatcher)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(AppRegisterType::class, $user, [
            'isRegistration' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $this->manager->persist($user);
            $this->manager->flush();

            $this->dispatcher->dispatch(new UserRegisteredEvent($user));

            $this->addFlash('success', 'Votre compte a bien été créé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
