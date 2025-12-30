<?php

namespace App\Security\Voter;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReservationVoter extends Voter
{
    public const CREATE = 'RESERVATION_CREATE';
    public const VIEW = 'RESERVATION_VIEW';
    public const CANCEL = 'RESERVATION_CANCEL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::VIEW, self::CANCEL])
            && ($subject instanceof Ouvrage || $subject instanceof Reservation);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::CREATE => $this->canCreate($user, $subject),
            self::VIEW => $this->canView($user, $subject),
            self::CANCEL => $this->canCancel($user, $subject),
            default => false,
        };
    }

    private function canCreate(User $user, Ouvrage $ouvrage): bool
    {
        // Seuls les membres PURS (pas les bibliothécaires ni admins) peuvent réserver
        // On vérifie le rôle EXACT stocké en base, pas les rôles hérités
        $userRole = $user->getRole();
        return $userRole === 'ROLE_MEMBER';
    }

    private function canView(User $user, Reservation $reservation): bool
    {
        // Un membre peut voir ses propres réservations
        if ($reservation->getUser() === $user) {
            return true;
        }

        // Un bibliothécaire ou admin peut voir toutes les réservations
        // Ici on peut utiliser isGranted car on veut la hiérarchie
        $userRole = $user->getRole();
        return in_array($userRole, ['ROLE_LIBRARIAN', 'ROLE_ADMIN']);
    }

    private function canCancel(User $user, Reservation $reservation): bool
    {
        $userRole = $user->getRole();
        
        // Un bibliothécaire ou admin peut annuler n'importe quelle réservation
        if (in_array($userRole, ['ROLE_LIBRARIAN', 'ROLE_ADMIN'])) {
            return true;
        }

        // Un membre peut annuler ses propres réservations
        return $reservation->getUser() === $user && $userRole === 'ROLE_MEMBER';
    }
}
