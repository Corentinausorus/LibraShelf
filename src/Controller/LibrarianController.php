<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur principal pour l'espace bibliothécaire.
 * 
 * Ce contrôleur gère les vues de tableau de bord et de navigation.
 * Les opérations CRUD spécifiques sont déléguées aux contrôleurs dédiés :
 * - OuvrageController : gestion des ouvrages
 * - ExemplaireController : gestion des exemplaires
 * 
 * @see \App\Controller\Librarian\OuvrageController
 * @see \App\Controller\Librarian\ExemplaireController
 */
#[Route('/librarian')]
#[IsGranted('ROLE_LIBRARIAN')]
final class LibrarianController extends AbstractController
{
    /**
     * Affiche le tableau de bord du bibliothécaire.
     */
    #[Route('', name: 'librarian_dashboard')]
    public function index(): Response
    {
        return $this->render('librarian/dashboard.html.twig');
    }

    /**
     * Affiche la vue de gestion du catalogue.
     */
    #[Route('/catalog', name: 'librarian_catalog')]
    public function manageCatalog(): Response
    {
        return $this->render('librarian/catalog.html.twig');
    }

    /**
     * Affiche la vue de gestion des emprunts.
     */
    #[Route('/loans', name: 'librarian_loans')]
    public function manageLoans(): Response
    {
        return $this->render('librarian/loans.html.twig');
    }

    /**
     * Affiche la vue de gestion des membres.
     */
    #[Route('/members', name: 'librarian_members')]
    public function manageMembers(): Response
    {
        return $this->render('librarian/members.html.twig');
    }

    /**
     * Affiche la liste des réservations.
     */
    #[Route('/reservations', name: 'librarian_reservations')]
    public function manageReservations(ReservationRepository $reservationRepository): Response
    {
        return $this->render('librarian/reservations.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
}
