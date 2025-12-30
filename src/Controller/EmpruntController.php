<?php

namespace App\Controller;

use App\Service\ServiceReglesEmprunt;
use App\Entity\Emprunt;
use App\Entity\Reservation;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emprunt')]
#[IsGranted('ROLE_BIBLIOTHECAIRE')]
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
        // Vérifier que la réservation est bien confirmée
        if ($reservation->getStatut() !== 'confirmee') {
            $this->addFlash('error', 'Cette réservation ne peut pas être transformée en emprunt.');
            return $this->redirectToRoute('app_emprunt_index');
        }

        // Créer l'emprunt
        $emprunt = new Emprunt();
        $emprunt->setUtilisateur($reservation->getUtilisateur());
        $emprunt->setLivre($reservation->getLivre());
        $emprunt->setDateEmprunt(new \DateTimeImmutable());
        
        // Date de retour : 14 jours après l'emprunt
        $dateRetourPrevue = new \DateTimeImmutable('+14 days');
        $emprunt->setDateRetourPrevue($dateRetourPrevue);
        
        $emprunt->setStatut('en_cours');

        // Mettre à jour le statut du livre
        $livre = $reservation->getLivre();
        $livre->setDisponibilite(false);

        // Mettre à jour le statut de la réservation
        $reservation->setStatut('empruntee');

        $entityManager->persist($emprunt);
        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'L\'emprunt a été créé avec succès. Date de retour prévue : %s',
            $dateRetourPrevue->format('d/m/Y')
        ));

        return $this->redirectToRoute('app_emprunt_index');
    }

    #[Route('/annuler-reservation/{id}', name: 'app_emprunt_cancel_reservation', methods: ['POST'])]
    public function cancelReservation(
        Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($reservation->getStatut() !== 'confirmee') {
            $this->addFlash('error', 'Cette réservation ne peut pas être annulée.');
            return $this->redirectToRoute('app_emprunt_index');
        }

        $reservation->setStatut('annulee');
        $entityManager->flush();

        $this->addFlash('info', 'La réservation a été annulée.');

        return $this->redirectToRoute('app_emprunt_index');
    }
}
