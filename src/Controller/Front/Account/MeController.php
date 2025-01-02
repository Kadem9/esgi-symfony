<?php

namespace App\Controller\Front\Account;

use App\Form\AppRegisterType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mon-compte', name: 'app_account_')]
class MeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AppRegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
        }

        return $this->render('front/account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/commandes', name: 'orders')]
    public function orders(OrderRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $orders = $repository->findBy(['user' => $user]);
        $orders = $paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('front/account/order.html.twig', [
            'orders' => $orders
        ]);
    }
}