<?php

namespace App\Controller\Front\Order;

use App\Entity\Event;
use App\Entity\Order;
use App\Service\Order\OrderService;
use App\Service\Stripe\StripeService;
use App\Service\User\CurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/order', name: 'app_order_')]
class OrderController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/{id}/pay', name: 'pay', requirements: ['id' => '\d+'])]
    public function pay(Event $event, StripeService $stripeService, CurrentUserService $currentUserService, OrderService $orderService): Response
    {
        $user = $currentUserService->getCurrentUser();
        if (!$user) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $order = $orderService->createOrder($user, $event);

        $successUrl = $this->generateUrl('app_order_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?sessionId={CHECKOUT_SESSION_ID}';
        $cancelUrl = $this->generateUrl('app_order_cancel', ['eventId' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $event->getName(),
                    ],
                    'unit_amount' => $event->getPrice() * 100,
                ],
                'quantity' => 1,
            ]
        ];

        $session = $stripeService->createCheckoutSession($successUrl, $cancelUrl, $lineItems, $order->getId());

        $orderService->attachCheckoutSessionId($order, $session->id);
        return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
    }

    #[Route('/success', name: 'success')]
    public function orderSuccess(Request $request, StripeService $stripeService, OrderService $orderService): Response
    {
        $sessionId = $request->query->get('sessionId');

        if (!$sessionId) {
            $this->addFlash('error', 'Session Stripe invalide.');
            return $this->redirectToRoute('app_event_home');
        }

        try {
            $session = $stripeService->getSession($sessionId);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la validation Stripe.');
            return $this->redirectToRoute('app_event_home');
        }

        if ($session->payment_status !== 'paid') {
            $this->addFlash('error', 'Le paiement n\'a pas été validé.');
            return $this->redirectToRoute('app_event_home');
        }

        $orderId = $session->metadata['order_id'];
        $order = $this->manager->getRepository(Order::class)->find($orderId);

        if (!$order || $order->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Commande invalide.');
            return $this->redirectToRoute('app_event_home');
        }

        $orderService->updateOrderPaymentStatus($order, true);

        return $this->render('front/order/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/cancel', name: 'cancel')]
    public function orderCancel(Request $request): Response
    {
        $eventId = $request->query->get('eventId');
        $event = $this->manager->getRepository(Event::class)->find($eventId);

        if (!$event) {
            $this->addFlash('error', 'Événement invalide.');
            return $this->redirectToRoute('app_event_home');
        }

        return $this->render('front/order/cancel.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/refund', name: 'refund')]
    public function orderRefund(Request $request, OrderService $orderService): Response
    {
        $orderId = $request->query->get('orderId');
        $order = $this->manager->getRepository(Order::class)->find($orderId);

        if ($order) {
            $orderService->updateOrderPaymentStatus($order, false);
        }

        return $this->render('front/order/refused.html.twig', [
            'order' => $order,
        ]);
    }
}
