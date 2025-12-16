<?php

namespace App\Controller\Librarian;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Form\ExemplaireType;
use App\Repository\ExemplairesRepository;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur dédié à la gestion des exemplaires par les bibliothécaires.
 * 
 * Ce contrôleur gère toutes les opérations CRUD sur les exemplaires :
 * - Liste des exemplaires (tous ou par ouvrage)
 * - Création d'un nouvel exemplaire
 * - Modification d'un exemplaire existant
 * - Suppression d'un exemplaire
 */
#[Route('/librarian/exemplaire')]
#[IsGranted('ROLE_LIBRARIAN')]
final class ExemplaireController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExemplairesRepository $exemplairesRepository,
        private readonly OuvrageRepository $ouvrageRepository
    ) {
    }

    /**
     * Affiche la liste de tous les exemplaires.
     */
    #[Route('s', name: 'librarian_exemplaires')]
    public function index(): Response
    {
        return $this->render('librarian/exemplaires/index.html.twig', [
            'exemplaires' => $this->exemplairesRepository->findAll(),
            'ouvrage' => null,
        ]);
    }

    /**
     * Affiche les exemplaires d'un ouvrage spécifique.
     */
    #[Route('s/ouvrage/{id}', name: 'librarian_ouvrage_exemplaires')]
    public function listByOuvrage(Ouvrage $ouvrage): Response
    {
        return $this->render('librarian/exemplaires/index.html.twig', [
            'exemplaires' => $ouvrage->getExemplaires(),
            'ouvrage' => $ouvrage,
        ]);
    }

    /**
     * Affiche le formulaire de création et traite la soumission.
     * 
     * @param int|null $ouvrageId ID de l'ouvrage à pré-sélectionner (optionnel)
     */
    #[Route('/new/{ouvrageId?}', name: 'librarian_exemplaire_new')]
    public function new(Request $request, ?int $ouvrageId = null): Response
    {
        $exemplaire = $this->createExemplaireWithDefaults($ouvrageId);

        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($exemplaire);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'exemplaire a été créé avec succès.');
            return $this->redirectToExemplaireList($exemplaire);
        }

        return $this->render('librarian/exemplaires/form.html.twig', [
            'form' => $form->createView(),
            'exemplaire' => $exemplaire,
            'isEdit' => false,
        ]);
    }

    /**
     * Affiche le formulaire d'édition et traite la soumission.
     */
    #[Route('/{id}/edit', name: 'librarian_exemplaire_edit')]
    public function edit(Request $request, Exemplaires $exemplaire): Response
    {
        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'exemplaire a été modifié avec succès.');
            return $this->redirectToExemplaireList($exemplaire);
        }

        return $this->render('librarian/exemplaires/form.html.twig', [
            'form' => $form->createView(),
            'exemplaire' => $exemplaire,
            'isEdit' => true,
        ]);
    }

    /**
     * Supprime un exemplaire après vérification du token CSRF.
     */
    #[Route('/{id}/delete', name: 'librarian_exemplaire_delete', methods: ['POST'])]
    public function delete(Request $request, Exemplaires $exemplaire): Response
    {
        $ouvrageId = $exemplaire->getOuvrage()?->getId();
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $exemplaire->getId(), $token)) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('librarian_exemplaires');
        }

        $this->entityManager->remove($exemplaire);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'exemplaire a été supprimé avec succès.');

        if ($ouvrageId) {
            return $this->redirectToRoute('librarian_ouvrage_exemplaires', ['id' => $ouvrageId]);
        }

        return $this->redirectToRoute('librarian_exemplaires');
    }

    /**
     * Crée un nouvel exemplaire avec les valeurs par défaut.
     */
    private function createExemplaireWithDefaults(?int $ouvrageId): Exemplaires
    {
        $exemplaire = new Exemplaires();
        $exemplaire->setDisponible(true);

        if ($ouvrageId) {
            $ouvrage = $this->ouvrageRepository->find($ouvrageId);
            if ($ouvrage) {
                $exemplaire->setOuvrage($ouvrage);
            }
        }

        return $exemplaire;
    }

    /**
     * Redirige vers la liste appropriée selon l'ouvrage associé.
     */
    private function redirectToExemplaireList(Exemplaires $exemplaire): Response
    {
        if ($exemplaire->getOuvrage()) {
            return $this->redirectToRoute('librarian_ouvrage_exemplaires', [
                'id' => $exemplaire->getOuvrage()->getId()
            ]);
        }

        return $this->redirectToRoute('librarian_exemplaires');
    }
}
