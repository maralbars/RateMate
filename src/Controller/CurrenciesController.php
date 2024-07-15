<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CurrenciesController extends AbstractController
{
    #[Route('/currencies', name: 'app_currencies')]
    public function index(): Response
    {
        return $this->json([]);
    }
}
