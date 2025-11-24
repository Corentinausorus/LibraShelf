<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LibrarianController extends AbstractController
{
    #[Route('/librarian', name: 'librarian_dashboard')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function index(): Response
    {
        return $this->render('librarian/dashboard.html.twig');
    }

    #[Route('/librarian/catalog', name: 'librarian_catalog')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageCatalog(): Response
    {
        return $this->render('librarian/catalog.html.twig');
    }

    #[Route('/librarian/loans', name: 'librarian_loans')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageLoans(): Response
    {
        return $this->render('librarian/loans.html.twig');
    }

    #[Route('/librarian/members', name: 'librarian_members')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageMembers(): Response
    {
        return $this->render('librarian/members.html.twig');
    }

}
