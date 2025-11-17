<?php

namespace App\Security\Voter;

use App\Entity\Ouvrage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OuvrageVoter extends Voter
{
    public const VIEW = 'OUVRAGE_VIEW';
    public const EDIT = 'OUVRAGE_EDIT';
    public const DELETE = 'OUVRAGE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Ouvrage;
    }

    protected function voteOnAttribute(string $attribute, mixed $ouvrage, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false; 
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_MEMBER', $user->getRoles())) {
            return $attribute === self::VIEW;
        }

        if (in_array('ROLE_LIBRARIAN', $user->getRoles())) {
            
            if ($attribute === self::VIEW) {
                return true;
            }

            if ($attribute === self::EDIT) {
                return true;
            }

            if ($attribute === self::DELETE) {
                return $ouvrage->getCreatedBy() === $user;
            }
        }

        return false;
    }
}
