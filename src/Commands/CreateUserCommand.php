<?php


namespace App\Commands;


use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'user:create';

    /**
     * @var SodiumPasswordEncoder
     */
    private $coder;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(string $salt, EntityManagerInterface $entityManager) {
        $this->coder = new SodiumPasswordEncoder();
        $this->salt = $salt;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure() {
        $this
            ->addOption('name', 'i', InputOption::VALUE_REQUIRED)
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED)
            ->addOption('role', 'r', InputOption::VALUE_OPTIONAL);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $user = new User();
        $user->setName($input->getOption('name'))
            ->setPassword($this->coder->encodePassword($input->getOption('password'), $this->salt));

        if ($input->getOption('role') === 'admin') {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('Created!');
    }
}