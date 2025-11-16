<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;

final class OuvrageController extends AbstractController
{
    /*
    #[Route('/ouvrage', name: 'app_ouvrage')]
    public function index(): Response
    {
        return $this->render('ouvrage/index.html.twig', [
            'controller_name' => 'OuvrageController',
        ]);
    }
        */

    #[Route('/ouvrage', name: 'app_ouvrage_new')]
    public function index(OuvrageRepository $ouvrageRepository): Response
    {
        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrageRepository->findAll(),
        ]);
    }
}
