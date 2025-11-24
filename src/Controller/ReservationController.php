<?php

namespace App\Controller;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // CSRF check (form must submit _token)
        if (!$this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Requête invalide.');
            return $this->redirectToRoute('member_reservations');
        }

        // ensure the reservation belongs to the current user
        if ($reservation->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('member_reservations');
        }

        // if an exemplaire was reserved, mark it available again
        $ex = $reservation->getExemplaire();
        if ($ex !== null) {
            $ex->setDisponible(true);
            $entityManager->persist($ex);
        }

        // remove the reservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation annulée. L\'exemplaire est de nouveau disponible.');
        return $this->redirectToRoute('member_reservations');
    }

    #[Route('/add/{id}', name: 'app_reservation_add', methods: ['GET'])]
    public function add(Ouvrage $ouvrage, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $exemplaireDispo = null;
        foreach ($ouvrage->getExemplaires() as $ex) {
            if ($ex->isDisponible()) {
                $exemplaireDispo = $ex;
                break; 
            }
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setOuvrage($ouvrage);
        $reservation->setCreationDate(new \DateTimeImmutable());

        if ($exemplaireDispo) {
            $reservation->setExemplaire($exemplaireDispo);
            $reservation->setStatut('À récupérer'); 
            
            $exemplaireDispo->setDisponible(false); 
            
            $this->addFlash('success', 'Livre mis de côté ! Vous avez 48h pour venir le chercher.');
        } else {
            $reservation->setExemplaire(null); 
            $reservation->setStatut('En attente');

            $this->addFlash('info', 'Aucun exemplaire disponible. Vous avez rejoint la file d\'attente.');
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('member_reservations');
    }
}
