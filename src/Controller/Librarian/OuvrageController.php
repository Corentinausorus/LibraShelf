<?php

namespace App\Controller\Librarian;

use App\Entity\Ouvrage;
use App\Form\OuvrageType;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur dédié à la gestion des ouvrages par les bibliothécaires.
 * 
 * Ce contrôleur gère toutes les opérations CRUD sur les ouvrages :
 * - Liste des ouvrages
 * - Création d'un nouvel ouvrage
 * - Modification d'un ouvrage existant
 * - Suppression d'un ouvrage
 */
#[Route('/librarian/ouvrage')]
#[IsGranted('ROLE_LIBRARIAN')]
final class OuvrageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OuvrageRepository $ouvrageRepository
    ) {
    }

    /**
     * Affiche la liste de tous les ouvrages.
     */
    #[Route('s', name: 'librarian_ouvrages')]
    public function index(): Response
    {
        return $this->render('librarian/ouvrages/index.html.twig', [
            'ouvrages' => $this->ouvrageRepository->findAll(),
        ]);
    }

    /**
     * Affiche le formulaire de création et traite la soumission.
     */
    #[Route('/new', name: 'librarian_ouvrage_new')]
    public function new(Request $request): Response
    {
        $ouvrage = new Ouvrage();
        $form = $this->createForm(OuvrageType::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUnmappedFields($form, $ouvrage);
            $ouvrage->setCreatedBy($this->getUser());

            $this->entityManager->persist($ouvrage);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'ouvrage a été créé avec succès.');
            return $this->redirectToRoute('librarian_ouvrages');
        }

        return $this->render('librarian/ouvrages/form.html.twig', [
            'form' => $form->createView(),
            'ouvrage' => $ouvrage,
            'isEdit' => false,
        ]);
    }

    /**
     * Affiche le formulaire d'édition et traite la soumission.
     */
    #[Route('/{id}/edit', name: 'librarian_ouvrage_edit')]
    public function edit(Request $request, Ouvrage $ouvrage): Response
    {
        $form = $this->createForm(OuvrageType::class, $ouvrage);

        // Pré-remplir les champs non mappés
        $this->prefillUnmappedFields($form, $ouvrage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUnmappedFields($form, $ouvrage);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'ouvrage a été modifié avec succès.');
            return $this->redirectToRoute('librarian_ouvrages');
        }

        return $this->render('librarian/ouvrages/form.html.twig', [
            'form' => $form->createView(),
            'ouvrage' => $ouvrage,
            'isEdit' => true,
        ]);
    }

    /**
     * Supprime un ouvrage après vérification du token CSRF.
     */
    #[Route('/{id}/delete', name: 'librarian_ouvrage_delete', methods: ['POST'])]
    public function delete(Request $request, Ouvrage $ouvrage): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $ouvrage->getId(), $token)) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('librarian_ouvrages');
        }

        $this->entityManager->remove($ouvrage);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'ouvrage a été supprimé avec succès.');
        return $this->redirectToRoute('librarian_ouvrages');
    }

    /**
     * Gère les champs non mappés du formulaire lors de la soumission.
     */
    private function handleUnmappedFields($form, Ouvrage $ouvrage): void
    {
        if ($form->has('Langues') && $form->get('Langues')->getData()) {
            $ouvrage->setLangues($form->get('Langues')->getData());
        }

        if ($form->has('annee') && $form->get('annee')->getData()) {
            $ouvrage->setAnnee($form->get('annee')->getData());
        }
    }

    /**
     * Pré-remplit les champs non mappés pour l'édition.
     */
    private function prefillUnmappedFields($form, Ouvrage $ouvrage): void
    {
        if ($ouvrage->getLangues()) {
            $form->get('Langues')->setData($ouvrage->getLangues());
        }

        if ($ouvrage->getAnnee()) {
            $form->get('annee')->setData($ouvrage->getAnnee());
        }
    }
}
