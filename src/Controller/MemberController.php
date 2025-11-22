<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MemberController extends AbstractController
{
    #[Route('/member', name: 'member_dashboard')]
    #[IsGranted('ROLE_MEMBER')]
    public function index(): Response
    {
        return $this->render('member/dashboard.html.twig');
    }

    #[Route('/member/books', name: 'member_books')]
    #[IsGranted('ROLE_MEMBER')]
    public function books(): Response
    {
        // Liste des livres disponibles
        return $this->render('member/books.html.twig');
    }

    #[Route('/member/loans', name: 'member_loans')]
    #[IsGranted('ROLE_MEMBER')]
    public function myLoans(): Response
    {
        // Mes emprunts en cours
        return $this->render('member/loans.html.twig');
    }

    #[Route('/member/reservations', name: 'member_reservations')]
    #[IsGranted('ROLE_MEMBER')]
    public function myReservations(): Response
    {
        // Mes réservations
        return $this->render('member/reservations.html.twig');
    }
}
