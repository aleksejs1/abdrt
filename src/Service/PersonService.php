<?php

namespace App\Service;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\PersonRepository;
use DateTime;

class PersonService
{
    private $personRepository;
    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    public function getBirthdaySortedList(User $user)
    {
        $persons = $this->personRepository->getUserPersons($user);
        uasort($persons, function (Person $a, Person $b) {
            return $this->getNextBirthdayDate($a->getBirthday()) > $this->getNextBirthdayDate($b->getBirthday());
        });

        return $persons;
    }

    private function getNextBirthdayDate(DateTime $birthday)
    {
        $date = clone $birthday;
        $date->modify('+' . date('Y') - $date->format('Y') . ' years');
        $today = new DateTime();
        if($date < $today && $date->format('d.m') !== $today->format('d.m')) {
            $date->modify('+1 year');
        }

        return $date;
    }
}
