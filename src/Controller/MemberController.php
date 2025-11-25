<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;
use App\Repository\ReservationRepository;
use App\Repository\CategorieRepository;
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
    public function Ouvrages(
        Request $request,
        OuvrageRepository $ouvrageRepository,
        ReservationRepository $reservationRepository,
        \App\Repository\CategorieRepository $categorieRepository
    ): Response
    {
        // Récupération des filtres depuis la requête
        $filters = [
            'titre' => $request->query->get('titre', ''),
            'categorie' => $request->query->get('categorie', ''),
            'langue' => $request->query->get('langue', ''),
            'annee' => $request->query->get('annee', ''),
            'disponible' => $request->query->get('disponible', ''),
        ];

        $hasFilters = array_filter($filters, fn($v) => $v !== '');

        if ($hasFilters) {
            $ouvrages = $ouvrageRepository->searchWithFilters($filters);
        } else {
            $ouvrages = $ouvrageRepository->findAll();
        }

        // données pour les filtres
        $categories = $categorieRepository->findAll();
        $langues = $ouvrageRepository->findAllLangues();
        $annees = $ouvrageRepository->findAllAnnees();

        // réservations utilisateur (ids d'ouvrages déjà réservés)
        $reservedOuvrageIds = [];
        if ($this->getUser()) {
            $userReservations = $reservationRepository->findBy(['user' => $this->getUser()]);
            foreach ($userReservations as $r) {
                $ex = $r->getExemplaire();
                if ($ex && $ex->getOuvrage()) {
                    $reservedOuvrageIds[] = $ex->getOuvrage()->getId();
                }
            }
        }

        return $this->render('member/ouvrages.html.twig', [
            'ouvrages' => $ouvrages,
            'categories' => $categories,
            'langues' => $langues,
            'annees' => $annees,
            'filters' => $filters,
            'resultCount' => count($ouvrages),
            'reservedOuvrageIds' => $reservedOuvrageIds,
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
        $reservation->setCreationDate(new \DateTimeImmutable());
        $reservation->setUser($this->getUser());

        // required non-nullable column
        $reservation->setStatut('A venir chercher'); 

        $reservation->setExemplaire($exemplaire);

        // ensure the required ouvrage relation is set
        $reservation->setOuvrage($exemplaire->getOuvrage());

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

        // required non-nullable column
        $reservation->setStatut('Réserfé'); // <-- adapt to your allowed values

        // set the ouvrage to satisfy NOT NULL constraint
        $reservation->setOuvrage($ouvrage);

        // Optionnel : lier un exemplaire disponible immédiatement
        $disponible = $ouvrage->getExemplaires()->filter(fn($ex) => $ex->isDisponible());
        if (!$disponible->isEmpty()) {
            $reservation->setExemplaire($disponible->first());
            $disponible->first()->setDisponible(false);
        }

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été enregistrée !');

        return $this->redirectToRoute('member_reservations');
    }

    #[Route('/member/ouvrages/{id}', name: 'member_ouvrage_detail')]
    #[IsGranted('ROLE_MEMBER')]
    public function ouvrageDetail(int $id, OuvrageRepository $ouvrageRepo, ReservationRepository $reservationRepo): Response
    {
        $ouvrage = $ouvrageRepo->find($id);
        if (!$ouvrage) {
            $this->addFlash('error', 'Ouvrage introuvable.');
            return $this->redirectToRoute('member_ouvrages');
        }

        $user = $this->getUser();
        $reservations = $user ? $reservationRepo->findBy(['user' => $user, 'active' => true]) : [];

        // map reservations to ouvrage IDs (adjust accessors to your model)
        $reservedOuvrageIds = array_map(function($r) {
            return $r->getExemplaire()->getOuvrage()->getId();
        }, $reservations);

        return $this->render('member/ouvrage_detail.html.twig', [
            'ouvrage' => $ouvrage,
            'reservedOuvrageIds' => $reservedOuvrageIds,
        ]);
    }

    #[Route('/reservation/{id}/cancel', name: 'member_reservation_cancel')]
    #[IsGranted('ROLE_MEMBER')]
    public function cancelReservation(Reservation $reservation, EntityManagerInterface $em): Response
    {
        // ensure the reservation belongs to the current user
        if ($reservation->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('member_reservations');
        }

        // if an exemplaire was reserved, mark it available again
        $ex = $reservation->getExemplaire();
        if ($ex !== null) {
            $ex->setDisponible(true);
            $em->persist($ex);
        }

        // remove the reservation (or update statut / active flag if you prefer)
        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation annulée. L\'exemplaire est de nouveau disponible.');
        return $this->redirectToRoute('member_reservations');
    }
}
