<?php

namespace App\Service\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class StripeService
{
    public function __construct(
        #[Autowire('%stripe_key%')] private string $stripeSecretKey,
    )
    {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createCheckoutSession(string $successUrl, string $cancelUrl, array $lineItems, int $orderId): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'order_id' => $orderId,
            ],
        ]);
    }

    public function getSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }
}
