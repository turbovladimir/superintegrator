<?php


namespace App\Commands;


use App\Repository\UserRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteUserCommand extends Command
{
    protected static $defaultName = 'user:delete';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRoleRepository
     */
    private $userRepo;

    public function __construct(UserRoleRepository $userRepo, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    protected function configure() {
        $this
            ->addOption('name', 'i', InputOption::VALUE_REQUIRED);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$user = $this->userRepo->findByName($input->getOption('name'))) {
            $output->writeln('user not found!');

            exit();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $output->writeln('Deleted!');
    }
}