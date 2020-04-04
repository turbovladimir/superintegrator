<?php


namespace App\Commands\Environment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class NginxInitializerCommand
 *
 * @package App\Commands\Environment
 */
class NginxInitializerCommand extends Command
{
    const NGINX_SITE_CONFIG = APPLICATION_PATH . '/nginx/superintegrator.conf';
    
    const MACROS_PATTERN = '#\$\{<ENV_PARAMETER>\}#';
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = self::NGINX_SITE_CONFIG;
        
        if (!is_readable($configFile)) {
            $output->writeln("File `{$configFile}` is not readable!");
            return;
        }
        
        $this->replaceMacrosInFile($configFile);
    }
    
    /**
     * @param $file
     */
    private function replaceMacrosInFile($file)
    {
        $env = getenv();
        $content = file_get_contents($file);
    
        foreach ($env as $parameter => $value) {
            $pattern = str_replace('<ENV_PARAMETER>', $parameter,self::MACROS_PATTERN);
            preg_replace($pattern, $value, $content);
        }
    
        file_put_contents($file, $content);
    }
}