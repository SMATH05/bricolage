<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment/checkout/{id}', name: 'app_payment_checkout')]
    public function checkout(Product $product, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Use test key if env var not set, for safety
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_51...Placeholder';
        Stripe::setApiKey($stripeSecretKey);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getTitle(),
                            'images' => $product->getImage() ? [$this->getParameter('base_url') . '/uploads/products/' . $product->getImage()] : [],
                        ],
                        'unit_amount' => (int) ($product->getPrice() * 100), // Amount in cents
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', ['session_id' => '{CHECKOUT_SESSION_ID}'], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // Create a pending order
        $commande = new Commande();
        $commande->setUser($this->getUser());
        $commande->setProduct($product);
        $commande->setAmount($product->getPrice());
        $commande->setStatus('PENDING');
        $commande->setTrackingId($checkoutSession->id);

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirect($checkoutSession->url, 303);
    }

    #[Route('/payment/success', name: 'app_payment_success')]
    public function success(EntityManagerInterface $entityManager): Response
    {
        // ideally verify session_id with Stripe here

        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'app_payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
