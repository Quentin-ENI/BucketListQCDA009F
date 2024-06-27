<?php

namespace App\Controller;

use App\Form\EventType;
use App\Model\SearchEvent;
use App\Service\SearchEventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    #[Route('/event', name: 'event_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        SearchEventService $searchEventService
    ): Response
    {
        $searchEvent = new SearchEvent();
        $searchEvent->setStartDate(new \DateTimeImmutable());
        $form = $this->createForm(EventType::class, $searchEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $events = $searchEventService->searchEvents($searchEvent);
        } else {
            $events = [];
        }

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'form' => $form
        ]);
    }
}
