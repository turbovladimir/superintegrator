<?php


namespace App\Services\Tools;

use App\Response\ResponseMessage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tools\Cryptor;

class CryptorTool implements Tool
{
    /**
     * @var Cryptor
     */
    private $cryptor;

    public function __construct() {
        $this->cryptor = new Cryptor();
    }

    public function getToolInfo(): array {
        return [
            'title' => 'Cryptor',
            'description' => 'Encrypt/decrypt string by salt'
        ];
    }

    public function process(array $parameters, $action = null) {
        if (empty($parameters['row']) && empty($parameters['salt'])) {
            throw new BadRequestHttpException('Where are no required parameters from form');
        }

        if ($action === 'encrypt') {
            return new ResponseMessage($this->cryptor->encrypt($parameters['row'], $parameters['salt']));
        } elseif ($action === 'decrypt') {
            return new ResponseMessage($this->cryptor->decrypt($parameters['row'], $parameters['salt']));
        } else {
            throw new BadRequestHttpException('Incorrect action type!');
        }
    }
}