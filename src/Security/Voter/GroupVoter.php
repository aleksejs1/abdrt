<?php

namespace App\Security\Voter;

use App\Entity\Group;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    public const GROUP = 'GROUP';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::GROUP])
            && $subject instanceof Group;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::GROUP:
                return $subject->getUsers()->contains($user);
        }

        return false;
    }
}
