<?php

namespace App\Controller\Admin\Event;

use App\Entity\AdminEventType;
use App\Entity\Event;
use App\Form\AdminEventType as FormAdminEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\ValueResolver\Attribute\MapFormSearch;
use App\Helper\FormSearchHelper;
use App\Helper\Paginator\PaginatorInterface;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/event', name: 'admin_event_')]
class EventController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('/', name: 'index')]
    public function index(
        #[MapFormSearch(searchNames: ['name', 'date'], defaultSort: ['sort' => 'e.name', 'sortDir' => 'ASC'])] FormSearchHelper $formSearch,
        EventRepository $eventRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $query = $eventRepository->querySearch($formSearch);
        $count = $eventRepository->querySearch($formSearch, true)->getQuery()->getSingleScalarResult();
        $events = $paginator->paginate($query->getQuery());

        return $this->render('admin/event/index.html.twig', [
            'events' => $events,
            'formSearch' => $formSearch,
            'count' => $count,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(FormAdminEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setGenerateImageByFixture(false);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'évènement a été créé avec succès.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Event $event): Response
    {
        return $this->render('admin/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Event $event, Request $request): Response
    {
        $form = $this->createForm(FormAdminEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'évènement a été modifié avec succès.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
