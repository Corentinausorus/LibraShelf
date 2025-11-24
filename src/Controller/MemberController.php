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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    public function Ouvrages(OuvrageRepository $ouvrageRepository, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $ouvrages = $ouvrageRepository->findAll();

        // OPTIMISATION : Récupérer tous les IDs des ouvrages réservés en UNE SEULE requête
        $reservedOuvrageIds = [];
        if ($user) {
            // On utilise le QueryBuilder pour ne récupérer que les IDs, c'est ultra-rapide
            $reservations = $reservationRepository->createQueryBuilder('r')
                ->select('IDENTITY(r.ouvrage) as ouvrage_id') // On ne prend que l'ID
                ->where('r.user = :user')
                ->andWhere('r.statut IN (:statuts)')
                ->setParameter('user', $user)
                ->setParameter('statuts', ['En attente', 'À récupérer'])
                ->getQuery()
                ->getResult();

            // On transforme le résultat en un tableau simple d'IDs [12, 45, 89]
            $reservedOuvrageIds = array_column($reservations, 'ouvrage_id');
        }

        return $this->render('member/ouvrages.html.twig', [
            'ouvrages' => $ouvrages,
            'reservedOuvrageIds' => $reservedOuvrageIds, // On passe le tableau léger
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
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // On récupère les réservations de l'utilisateur, triées par date (plus récentes en haut)
        $reservations = $reservationRepository->findBy(
            ['user' => $user],
            ['creationDate' => 'DESC']
        );

        return $this->render('member/reservations.html.twig', [
            'reservations' => $reservations,
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

        $reservation->setExemplaire($exemplaire);
        // CORRECTION : On doit définir l'ouvrage lié à cet exemplaire
        $reservation->setOuvrage($exemplaire->getOuvrage());
        $reservation->setStatut('À récupérer'); // On définit un statut par défaut

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

        // --- CORRECTION DEBUT : Vérification plus fine ---
        // On récupère toutes les réservations de l'utilisateur pour ce livre
        $existingReservations = $reservationRepo->findBy([
            'user' => $this->getUser(),
            'ouvrage' => $ouvrage
        ]);

        // On regarde s'il y en a une qui est encore active
        foreach ($existingReservations as $res) {
            if (in_array($res->getStatut(), ['En attente', 'À récupérer'])) {
                $this->addFlash('info', 'Vous avez déjà une réservation en cours pour cet ouvrage.');
                return $this->redirectToRoute('member_reservations');
            }
        }
        // --- CORRECTION FIN ---

        $reservation = new Reservation();
        $reservation->setCreationDate(new \DateTimeImmutable());
        $reservation->setUser($this->getUser());
        $reservation->setOuvrage($ouvrage);

        // Optionnel : lier un exemplaire disponible immédiatement
        $disponible = $ouvrage->getExemplaires()->filter(fn($ex) => $ex->isDisponible());
        
        if (!$disponible->isEmpty()) {
            $reservation->setExemplaire($disponible->first());
            $disponible->first()->setDisponible(false);
            $reservation->setStatut('À récupérer');
            $this->addFlash('success', 'Exemplaire disponible ! Vous pouvez venir le chercher.');
        } else {
            $reservation->setStatut('En attente');
            // Important : s'assurer que l'exemplaire est bien null
            $reservation->setExemplaire(null); 
            $this->addFlash('success', 'Vous avez été ajouté à la file d\'attente.');
        }

        $em->persist($reservation);
        $em->flush();

        return $this->redirectToRoute('member_reservations');
    }

}
