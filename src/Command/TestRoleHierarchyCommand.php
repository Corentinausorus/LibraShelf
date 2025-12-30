<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[AsCommand(
    name: 'app:test-role-hierarchy',
    description: 'Teste la hiérarchie des rôles'
)]
class TestRoleHierarchyCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthorizationCheckerInterface $authChecker,
        private TokenStorageInterface $tokenStorage
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Test de la hiérarchie des rôles');

        // Récupérer un utilisateur de chaque type
        $member = $this->userRepository->findOneBy(['role' => 'ROLE_MEMBER']);
        $librarian = $this->userRepository->findOneBy(['role' => 'ROLE_LIBRARIAN']);
        $admin = $this->userRepository->findOneBy(['role' => 'ROLE_ADMIN']);

        $users = [
            'Member' => $member,
            'Librarian' => $librarian,
            'Admin' => $admin,
        ];

        foreach ($users as $type => $user) {
            if (!$user) {
                $io->warning("Aucun utilisateur $type trouvé.");
                continue;
            }

            $io->section("Utilisateur : $type ({$user->getEmail()})");
            
            // Simuler l'authentification
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);

            // Tester les permissions
            $table = [
                ['Permission', 'Résultat'],
                ['ROLE_MEMBER', $this->authChecker->isGranted('ROLE_MEMBER') ? '✅ Oui' : '❌ Non'],
                ['ROLE_LIBRARIAN', $this->authChecker->isGranted('ROLE_LIBRARIAN') ? '✅ Oui' : '❌ Non'],
                ['ROLE_ADMIN', $this->authChecker->isGranted('ROLE_ADMIN') ? '✅ Oui' : '❌ Non'],
            ];

            $io->table($table[0], array_slice($table, 1));

            $io->text([
                "Rôle exact en base : {$user->getRole()}",
                "Rôles hérités Symfony : " . implode(', ', $user->getRoles()),
            ]);
        }

        $io->newLine();
        $io->success('Test terminé !');
        $io->note([
            'Hiérarchie attendue :',
            '- Member : accès MEMBER uniquement',
            '- Librarian : accès MEMBER + LIBRARIAN',
            '- Admin : accès MEMBER + LIBRARIAN + ADMIN',
        ]);

        return Command::SUCCESS;
    }
}
