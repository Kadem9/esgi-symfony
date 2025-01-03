<?php

namespace App\Controller\Front;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('front/home/index.html.twig', [
            'popularEvents' => $eventRepository->findTopViewedEvents(),
        ]);
    }
}
