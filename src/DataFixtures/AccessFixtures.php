<?php

namespace App\DataFixtures;

use App\Entity\Access;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccessFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $access1 = new Access(
         $this->getReference(PersonFixtures::PERSON_REFERENCE_JOHN),
         $this->getReference(UserFixtures::USER_REFERENCE_1)
        );

        $access2 = new Access($this->getReference(PersonFixtures::PERSON_REFERENCE_BOB));
        $access2->setAccessGroup($this->getReference(GroupFixtures::GROUP_REFERENCE_FAMILY));

        $access3 = new Access(
            $this->getReference(PersonFixtures::PERSON_REFERENCE_JANE),
            $this->getReference(UserFixtures::USER_REFERENCE_2)
        );

        $manager->persist($access1);
        $manager->persist($access2);
        $manager->persist($access3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            GroupFixtures::class,
        ];
    }
}
