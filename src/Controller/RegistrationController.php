<?php

namespace App\Controller;

use App\Enum\Role;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params  // ← AJOUT ICI
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $librarianInviteCode = $_ENV['LIBRARIAN_INVITE_CODE'];
        if ($form->isSubmitted() && $form->isValid()) {
            $inviteCode = $form->get('inviteCode')->getData();
            $role = 'ROLE_MEMBER'; // Rôle par défaut
            
            // Vérifier les codes d'invitation
            if ($inviteCode === $librarianInviteCode) {
                $user->setRole(Role::LIBRARIAN->value);
                $this->addFlash('success', 'Bienvenue ! Vous êtes enregistré comme bibliothécaire.');
            } else {
                $this->addFlash('warning', 'Code d\'invitation invalide. Vous êtes inscrit comme membre.');
                $user->setRole(Role::MEMBER->value);

            }
                        
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre compte a été créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}