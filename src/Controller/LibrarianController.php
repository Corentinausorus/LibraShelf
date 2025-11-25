<?php

namespace App\Controller;

use App\Entity\Ouvrage;
use App\Form\OuvrageType;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LibrarianController extends AbstractController
{
    #[Route('/librarian', name: 'librarian_dashboard')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function index(): Response
    {
        return $this->render('librarian/dashboard.html.twig');
    }

    #[Route('/librarian/catalog', name: 'librarian_catalog')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageCatalog(): Response
    {
        return $this->render('librarian/catalog.html.twig');
    }

    #[Route('/librarian/loans', name: 'librarian_loans')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageLoans(): Response
    {
        return $this->render('librarian/loans.html.twig');
    }

    #[Route('/librarian/members', name: 'librarian_members')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manageMembers(): Response
    {
        return $this->render('librarian/members.html.twig');
    }

    #[Route('/librarian/ouvrages', name: 'librarian_ouvrages')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function listOuvrages(OuvrageRepository $ouvrageRepository): Response
    {
        return $this->render('librarian/ouvrages/index.html.twig', [
            'ouvrages' => $ouvrageRepository->findAll(),
        ]);
    }

    #[Route('/librarian/ouvrage/new', name: 'librarian_ouvrage_new')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function createOuvrage(Request $request, EntityManagerInterface $em): Response
    {
        $ouvrage = new Ouvrage();
        $form = $this->createForm(OuvrageType::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer les champs non mappés manuellement
            if ($form->has('Langues') && $form->get('Langues')->getData()) {
                $ouvrage->setLangues($form->get('Langues')->getData());
            }
            if ($form->has('annee') && $form->get('annee')->getData()) {
                $ouvrage->setAnnée($form->get('annee')->getData());
            }
            
            $ouvrage->setCreatedBy($this->getUser());
            $em->persist($ouvrage);
            $em->flush();

            $this->addFlash('success', 'L\'ouvrage a été créé avec succès.');
            return $this->redirectToRoute('librarian_ouvrages');
        }

        return $this->render('librarian/ouvrages/form.html.twig', [
            'form' => $form->createView(),
            'ouvrage' => $ouvrage,
            'isEdit' => false,
        ]);
    }

    #[Route('/librarian/ouvrage/{id}/edit', name: 'librarian_ouvrage_edit')]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function editOuvrage(Request $request, Ouvrage $ouvrage, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OuvrageType::class, $ouvrage);
        
        // Pré-remplir les champs non mappés
        if ($ouvrage->getLangues()) {
            $form->get('Langues')->setData($ouvrage->getLangues());
        }
        if ($ouvrage->getAnnée()) {
            $form->get('annee')->setData($ouvrage->getAnnée());
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer les champs non mappés manuellement
            if ($form->has('Langues')) {
                $ouvrage->setLangues($form->get('Langues')->getData());
            }
            if ($form->has('annee')) {
                $ouvrage->setAnnée($form->get('annee')->getData());
            }
            
            $em->flush();

            $this->addFlash('success', 'L\'ouvrage a été modifié avec succès.');
            return $this->redirectToRoute('librarian_ouvrages');
        }

        return $this->render('librarian/ouvrages/form.html.twig', [
            'form' => $form->createView(),
            'ouvrage' => $ouvrage,
            'isEdit' => true,
        ]);
    }

    #[Route('/librarian/ouvrage/{id}/delete', name: 'librarian_ouvrage_delete', methods: ['POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function deleteOuvrage(Request $request, Ouvrage $ouvrage, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ouvrage->getId(), $request->request->get('_token'))) {
            $em->remove($ouvrage);
            $em->flush();

            $this->addFlash('success', 'L\'ouvrage a été supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('librarian_ouvrages');
    }
}
