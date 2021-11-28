<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PersonFixtures extends Fixture
{
    public const PERSON_REFERENCE_JOHN = 'person-john';
    public const PERSON_REFERENCE_BOB = 'person-bob';
    public const PERSON_REFERENCE_JANE = 'person-jane';

    public function load(ObjectManager $manager): void
    {
         $john = new Person();
         $john->setName('john')->setBirthday(new \DateTime('30.06.1968'));

         $bob = new Person();
         $bob->setName('bob')->setBirthday(new \DateTime('16.05.1990'));

         $jane = new Person();
         $jane->setName('jane')->setBirthday(new \DateTime('03.06.1995'));

         $manager->persist($john);
         $manager->persist($bob);
         $manager->persist($jane);

        $manager->flush();

        $this->setReference(self::PERSON_REFERENCE_JOHN, $john);
        $this->setReference(self::PERSON_REFERENCE_BOB, $bob);
        $this->setReference(self::PERSON_REFERENCE_JANE, $jane);
    }
}
