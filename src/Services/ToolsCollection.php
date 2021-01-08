<?php


namespace App\Services;

use App\Services\Tools\Tool;
use Psr\Container\ContainerInterface;

class ToolsCollection
{
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getToolByName(string $name) : Tool {
        $name = "tools.{$name}";

        return $this->container->get($name);
    }
}