<?php

namespace App\Command;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PersonService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class AbdrtMailsSendCommand extends Command
{
    protected static $defaultName = 'abdrt:mails:send';
    protected static $defaultDescription = 'Send mail notifications';

    private $mailer;
    private $userRepository;
    private $personService;
    private $fromMail;

    public function __construct(
        string $name = null,
        MailerInterface $mailer,
        UserRepository $userRepository,
        PersonService $personService,
        string $fromMail
    ) {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->personService = $personService;
        $this->fromMail = $fromMail;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->getUsersWithEmails();
        $today = (new \DateTime())->format('d.m');

        /** @var User $user */
        foreach ($users as $user) {
            $persons = $this->personService->getBirthdaySortedList($user);
            /** @var Person $person */
            foreach ($persons as $person) {
                if (!$person->getBirthday() || $today !== $person->getBirthday()->format('d.m')) {
                    break;
                }

                $email = (new Email())
                    ->from(new Address($this->fromMail, 'abdrt'))
                    ->to($user->getEmail())
                    ->subject('Notification! ' . $person->getName() . ' has a birthday today!')
                    ->html(
                        '<p>' . $person->getName() . ' ' . $person->getSurname() . ' has a birthday today!</p>'
                        . '<p>Born in ' . $person->getBirthday()->format('d.m.Y') . '</p>'
                        . '<p>Age is ' . $this->getAges($person->getBirthday()->format('m/d/Y')) . '</p>'
                    )
                ;

                $this->mailer->send($email);
                $io->note('Mail sent to ' . $user->getEmail());
            }
        }

        $io->success('Sending done!');

        return Command::SUCCESS;
    }

    private function getAges(string $birthDate)
    {
        $parts = explode('/', $birthDate);
        $age = (date('md', date('U', mktime(0, 0, 0, $parts[0], $parts[1], $parts[2]))) > date('md')
            ? ((date('Y') - $parts[2]) - 1)
            : (date('Y') - $parts[2]));

        return $age;
    }
}
