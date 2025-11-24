<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/emprunt')]
final class EmpruntController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_emprunt_add', methods: ['GET'])]
    public function add(Exemplaires $exemplaire, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$exemplaire->isDisponible()) {
            $this->addFlash('danger', 'Cet exemplaire n\'est plus disponible.');
            return $this->redirectToRoute('member_ouvrages');
        }

        $emprunt = new Emprunt();
        
        // --- CORRECTION ICI ---
        // Remplacez setAdherent($user) par setUser($user)
        $emprunt->setUser($user); 
        // ----------------------

        $emprunt->setExemplaire($exemplaire);
        $emprunt->setDateEmprunt(new \DateTimeImmutable());
        $emprunt->setDateRetourPrevu((new \DateTimeImmutable())->modify('+15 days'));
        
        $exemplaire->setDisponible(false);

        $entityManager->persist($emprunt);
        $entityManager->flush();

        $this->addFlash('success', 'Emprunt validé pour l\'ouvrage : ' . $exemplaire->getOuvrage()->getTitre());

        return $this->redirectToRoute('member_ouvrages'); 
    }
}