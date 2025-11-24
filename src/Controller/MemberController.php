<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;
use App\Repository\ReservationRepository;
use App\Entity\Exemplaires;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MemberController extends AbstractController
{
    #[Route('/member', name: 'member_dashboard')]
    #[IsGranted('ROLE_MEMBER')]
    public function index(): Response
    {
        return $this->render('member/dashboard.html.twig');
    }

    #[Route('/member/ouvrages', name: 'member_ouvrages')]
    #[IsGranted('ROLE_MEMBER')]
    public function Ouvrages(OuvrageRepository $ouvrageRepository): Response
    {
        // Liste des livres disponibles
        return $this->render('member/ouvrages.html.twig', [
            'ouvrages' => $ouvrageRepository->findAll(),
        ]);
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
    public function myReservations(ReservationRepository $reservationRepository): Response
    {
        // Mes réservations
        return $this->render('member/reservations.html.twig',[
            'reservations' => $reservationRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/exemplaire/{id}/reserve', name: 'member_reserve_exemplaire')]
    public function reserve(Exemplaires $exemplaire,EntityManagerInterface $em): Response 
    {

        if (!$exemplaire->isDisponible()) {
            $this->addFlash('error', 'Cet exemplaire est déjà réservé.');
            return $this->redirectToRoute('app_liste_livres');
        }

        $reservation = new Reservation();
        $reservation->setCreationDate(new \DateTime());
        $reservation->setUser($this->getUser());

        $reservation->addExemplaire($exemplaire);

        $exemplaire->setDisponible(false);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été enregistrée !');

        return $this->redirectToRoute('member_reservations');
    }

    #[Route('/ouvrage/{id}/reserve', name: 'member_reserve_ouvrage')]
    #[IsGranted('ROLE_MEMBER')]
    public function reserveOuvrage(int $id, OuvrageRepository $ouvrageRepo, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {
        $ouvrage = $ouvrageRepo->find($id);

        if (!$ouvrage) {
            $this->addFlash('error', 'Ouvrage introuvable.');
            return $this->redirectToRoute('member_ouvrages');
        }

        // Vérifie s'il y a déjà une réservation pour ce membre
        $existing = $reservationRepo->findOneBy([
            'user' => $this->getUser(),
            // On peut ajouter filtrage sur ouvrage ou exemplaires
        ]);

        if ($existing) {
            $this->addFlash('info', 'Vous avez déjà une réservation pour cet ouvrage.');
            return $this->redirectToRoute('member_reservations');
        }

        $reservation = new Reservation();
        $reservation->setCreationDate(new \DateTimeImmutable());
        $reservation->setUser($this->getUser());

        // Optionnel : lier un exemplaire disponible immédiatement
        $disponible = $ouvrage->getExemplaires()->filter(fn($ex) => $ex->isDisponible());
        if (!$disponible->isEmpty()) {
            $reservation->addExemplaire($disponible->first());
            $disponible->first()->setDisponible(false);
        }

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été enregistrée !');

        return $this->redirectToRoute('member_reservations');
    }

}
