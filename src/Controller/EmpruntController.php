<?php

namespace App\Controller;

use App\Service\ServiceReglesEmprunt;
use App\Entity\Emprunt;
use App\Entity\Reservation;
use App\Enum\StatutEmprunt;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\StatutReservation;

#[Route('/emprunt')]
#[IsGranted('ROLE_LIBRARIAN')]
final class EmpruntController extends AbstractController
{
    #[Route('/', name: 'app_emprunt_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        // Récupérer toutes les réservations confirmées (prêtes à être transformées en emprunts)
        $reservations = $reservationRepository->findBy(
            ['statut' => 'confirmee'],
            ['dateReservation' => 'DESC']
        );

        return $this->render('librarian/loans.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new', name: 'app_emprunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ServiceReglesEmprunt $serviceRegles): Response
    {
        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);
        $serviceRegles->applyDefaultDueDateIfMissing($emprunt);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($emprunt);
            $entityManager->flush();

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_show', methods: ['GET'])]
    public function show(Emprunt $emprunt): Response
    {
        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emprunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emprunt/edit.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($emprunt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/creer/{id}', name: 'app_emprunt_create', methods: ['POST'])]
    public function create(
        Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que la réservation n'est pas déjà terminée ou annulée
        if ($reservation->getStatut() === StatutReservation::TERMINEE) {
            $this->addFlash('error', 'Cette réservation a déjà été transformée en emprunt.');
            return $this->redirectToRoute('librarian_loans');
        }

        if ($reservation->getStatut() === StatutReservation::ANNULEE) {
            $this->addFlash('error', 'Cette réservation est annulée et ne peut pas être transformée en emprunt.');
            return $this->redirectToRoute('librarian_loans');
        }

        try {
            // Créer l'emprunt
            $emprunt = new Emprunt();
            $emprunt->setUser($reservation->getUser());
            $emprunt->setExemplaire($reservation->getExemplaire());
            $emprunt->setStartAt(new \DateTimeImmutable());
            
            // Date de retour : 14 jours après l'emprunt
            $dateRetourPrevue = new \DateTimeImmutable('+14 days');
            $emprunt->setDueAt($dateRetourPrevue);
            $emprunt->setStatus(StatutEmprunt::EN_COURS);

            // Marquer la réservation comme terminée
            $reservation->complete();

            $entityManager->persist($emprunt);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'L\'emprunt a été créé avec succès pour %s. Date de retour prévue : %s',
                $reservation->getUser()->getNom(),
                $dateRetourPrevue->format('d/m/Y')
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de l\'emprunt : ' . $e->getMessage());
        }

        return $this->redirectToRoute('librarian_loans');
    }

    #[Route('/annuler-reservation/{id}', name: 'app_emprunt_cancel_reservation', methods: ['POST'])]
    public function cancelReservation(
        Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($reservation->getStatut() === StatutReservation::TERMINEE) {
            $this->addFlash('error', 'Cette réservation a déjà été transformée en emprunt.');
            return $this->redirectToRoute('librarian_loans');
        }

        if ($reservation->getStatut() === StatutReservation::ANNULEE) {
            $this->addFlash('info', 'Cette réservation est déjà annulée.');
            return $this->redirectToRoute('librarian_loans');
        }

        $reservation->cancel();
        $entityManager->flush();

        $this->addFlash('info', 'La réservation a été annulée avec succès.');

        return $this->redirectToRoute('librarian_loans');
    }
}
