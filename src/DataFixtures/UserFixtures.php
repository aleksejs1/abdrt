<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE_1 = 'user-1';
    public const USER_REFERENCE_2 = 'user-2';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUsername('user1');
        $user1->setPassword($this->encoder->encodePassword($user1, 'pass'));

        $user2 = new User();
        $user2->setUsername('user2');
        $user2->setPassword($this->encoder->encodePassword($user1, 'pass'));

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();

        $this->setReference(self::USER_REFERENCE_1, $user1);
        $this->setReference(self::USER_REFERENCE_2, $user2);
    }
}
