<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'abdrt:user:create';

    private $userRepository;
    private $passwordEncoder;
    private $entityManager;

    public function __construct(
        string $name = null,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Create new user or update user password')
            ->addArgument('username', InputArgument::OPTIONAL, 'Username')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        if (!$username || !$password) {
            $io->error('Empty arguments.');

            return 1;
        }
        if ($username) {
            $io->note(sprintf('You passed an username: %s', $username));
        }
        if ($password) {
            $io->note(sprintf('You passed a password: %s', $password));
        }
        if ($email) {
            $io->note(sprintf('You passed a email: %s', $email));
        }
        $message = 'User updated';
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if (!$user) {
            $message = 'User created';
            $user = new User();
            $user
                ->setUsername($username)
            ;
        }
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setEmail($email);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success($message);

        return 0;
    }
}
