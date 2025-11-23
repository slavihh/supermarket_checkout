<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\CheckoutType;
use App\Service\Checkout\CheckoutServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class CheckoutController extends AbstractController
{
    #[Route('/', name: 'checkout', methods: ['GET', 'POST'])]
    public function index(Request $request, CheckoutServiceInterface $checkoutService): Response
    {
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        $result = null;
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $items = $form->get('items')->getData();

            try {
                $result = $checkoutService->checkout($items);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'result' => $result,
            'error' => $error,
        ]);
    }
}
