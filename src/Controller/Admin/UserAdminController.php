<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserRoleType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class UserAdminController extends AbstractController
{
    #[Route('/users', name: 'admin_users')]
    public function index(UserRepository $repo): Response
    {
        $users = $repo->findAll();
        return $this->render('admin/index.html.twig', ['users' => $users]);
    }

    #[Route('', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/users/{id}/edit-role', name: 'admin_user_edit_role')]
    public function editRole(Request $request, User $user, EntityManagerInterface $em, UserRepository $repo): Response
    {
        // Ne pas permettre à un admin de modifier son propre rôle depuis cette interface
        $currentUser = $this->getUser();
        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            $this->addFlash('warning', 'Vous ne pouvez pas changer votre propre rôle ici.');
            return $this->redirectToRoute('admin_users');
        }

        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = $form->get('role')->getData();

            // protection : empêcher la suppression du dernier admin
            if ($user->getRole() === 'ROLE_ADMIN' && $newRole !== 'ROLE_ADMIN') {
                $adminCount = $repo->countByRole('ROLE_ADMIN');
                if ($adminCount <= 1) {
                    $this->addFlash('danger', 'Impossible de supprimer le dernier administrateur.');
                    return $this->redirectToRoute('admin_users');
                }
            }

            $user->setRole($newRole);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Rôle mis à jour avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit_role.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}