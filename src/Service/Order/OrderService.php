<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class OrderService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createOrder(User $user, Event $event): Order
    {
        $order = new Order();
        $order->setUser($user);
        $order->setEvent($event);
        $order->setAmount($event->getPrice());
        $order->setPaid(false);
        $order->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function updateOrderPaymentStatus(Order $order, bool $isPaid): void
    {
        $order->setPaid($isPaid);
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function attachCheckoutSessionId(Order $order, string $checkoutSessionId): void
    {
        $order->setSessionStripe($checkoutSessionId);
        $this->entityManager->flush();
    }

}
