<?php


namespace App\Commands;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;

class DeleteUserCommand extends Command
{
    protected static $defaultName = 'user:delete';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(string $name = null, UserRepository $userRepo, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        parent::__construct($name);
    }

    protected function configure() {
        $this
            ->addOption('name', 'i', InputOption::VALUE_REQUIRED);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$user = $this->userRepo->findOneBy(['name' => $input->getOption('name')])) {
            $output->writeln('user not found!');

            exit();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $output->writeln('Deleted!');
    }
}