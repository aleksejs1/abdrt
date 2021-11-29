<?php

namespace App\Security\Voter;

use App\Entity\Person;
use App\Repository\AccessRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonVoter extends Voter
{
    public const PERSON = 'PERSON';

    private $accessRepository;

    public function __construct(AccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::PERSON])
            && $subject instanceof Person;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PERSON:
                return $this->accessRepository->hasAccess($user, $subject);
        }

        return false;
    }
}
