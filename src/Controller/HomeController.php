<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Si l'utilisateur est connecté, rediriger selon son rôle
        if ($this->getUser()) {
            $user = $this->getUser();
            
            // Redirection basée sur le rôle
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            }
            
            if ($this->isGranted('ROLE_LIBRARIAN')) {
                return $this->redirectToRoute('librarian_dashboard');
            }
            
            if ($this->isGranted('ROLE_MEMBER')) {
                return $this->redirectToRoute('member_dashboard');
            }
        }
        
        // Page publique pour les visiteurs non connectés
        return $this->render('home/index.html.twig');
    }
}
