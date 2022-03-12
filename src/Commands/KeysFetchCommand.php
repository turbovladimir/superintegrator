<?php


namespace App\Commands;


use App\Repository\TelebotKeyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\Cryptor;

class KeysFetchCommand extends Command
{
    protected static $defaultName = 'keys:fetch';
    /**
     * @var TelebotKeyRepository
     */
    private $telebotKeyRepository;
    /**
     * @var Cryptor
     */
    private $cryptor;

    public function __construct(TelebotKeyRepository $telebotKeyRepository) {
        parent::__construct();
        $this->telebotKeyRepository = $telebotKeyRepository;
        $this->cryptor = new Cryptor();
    }

    protected function configure() {
        $this
            ->addArgument('salt');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $keys = $this->telebotKeyRepository->findAll();

        foreach ($keys as $key) {
            $output->writeln(sprintf('owner id `%d` %s: %s',
                $key->getUserId(),
                $key->getName(),
                $this->cryptor->decrypt($key->getValue(), $input->getArgument('salt'))
            ));
        }
    }
}