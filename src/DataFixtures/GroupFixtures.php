<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public const GROUP_REFERENCE_FAMILY = 'group-family';

    public function load(ObjectManager $manager): void
    {
        $group = new Group();
        $group->setName('family');
        $group->addUser($this->getReference(UserFixtures::USER_REFERENCE_1));

        $manager->persist($group);
        $manager->flush();

        $this->setReference(self::GROUP_REFERENCE_FAMILY, $group);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
