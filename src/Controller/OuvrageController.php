<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OuvrageController extends AbstractController
{
    #[Route('/ouvrage', name: 'app_ouvrage')]
    public function index(): Response
    {
        return $this->render('ouvrage/index.html.twig', [
            'controller_name' => 'OuvrageController',
        ]);
    }
}
