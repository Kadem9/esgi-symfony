<?php

namespace App\Controller\Front\Event;

use App\Entity\Event;
use App\Helper\FormSearchHelper;
use App\Helper\Paginator\PaginatorInterface;
use App\Repository\EventRepository;
use App\ValueResolver\Attribute\MapFormSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/event', name: 'app_event_')]
class EventController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    #[Route('/', name: 'home')]
    public function index(
        #[MapFormSearch(searchNames: ['name', 'date'], defaultSort: ['sort' => 'e.name', 'sortDir' => 'ASC'])] FormSearchHelper $formSearch,
        EventRepository                                                                                                         $eventRepository,
        PaginatorInterface                                                                                                      $paginator
    ): Response
    {
        $query = $eventRepository->querySearch($formSearch);
        $count = $eventRepository->querySearch($formSearch, true)->getQuery()->getSingleScalarResult();
        $events = $paginator->paginate($query->getQuery());

        return $this->render('front/event/index.html.twig', [
            'events' => $events,
            'formSearch' => $formSearch,
            'count' => $count,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Event $event, Request $request): Response
    {
        $response = new Response();
        $cookieName = 'viewed_event_' . $event->getId();

        if (!$request->cookies->has($cookieName)) {
            $event->incrementClicks();
            $this->manager->flush();

            $response->headers->setCookie(new Cookie($cookieName, '1', strtotime('+1 day')));
        }

        $response->setContent(
            $this->renderView('front/event/show.html.twig', [
                'event' => $event,
            ])
        );

        return $response;
    }


}
