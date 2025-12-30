<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

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
    public function manageLoans(ReservationRepository $reservationRepository): Response
    {
        // Récupérer uniquement les réservations actives (non empruntées)
        $reservations = $reservationRepository->createQueryBuilder('r')
            ->where('r.statut != :terminee')
            ->andWhere('r.statut != :annulee')
            ->setParameter('terminee', \App\Enum\StatutReservation::TERMINEE)
            ->setParameter('annulee', \App\Enum\StatutReservation::ANNULEE)
            ->orderBy('r.creationDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('librarian/loans.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Affiche la vue de gestion des membres.
     */
    #[Route('/members', name: 'librarian_members')]
    public function manageMembers(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs
        $members = $userRepository->findAll();

        return $this->render('librarian/members.html.twig', [
            'members' => $members,
        ]);
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

    /**
     * Affiche les détails d'un membre.
     */
    #[Route('/members/{id}', name: 'librarian_member_detail')]
    public function memberDetail(User $member): Response
    {
        return $this->render('librarian/member_detail.html.twig', [
            'member' => $member,
        ]);
    }
}
