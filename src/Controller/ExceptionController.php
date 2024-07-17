<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionController extends AbstractController
{
    public function showException(\Throwable $exception): Response
    {
        if ($exception instanceof NotFoundHttpException) {
            return $this->render('errors/404.html.twig', [
                'exception' => $exception,
            ]);
        }

        // Handle other exceptions or return a generic error page
        return $this->render('errors/error.html.twig', [
            'exception' => $exception,
        ]);
    }
}