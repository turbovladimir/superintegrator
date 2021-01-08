<?php


namespace App\Services\Tools;


use App\Exceptions\Warning;
use App\Response\ResponseMessage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Cryptor implements Tool
{
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

        $salt = $parameters['salt'];
        $cipherMethod = 'aes-128-ctr';
        
        if ($action === 'encrypt') {
            $token = $parameters['row'];
            $encIv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod));
            $data = openssl_encrypt($token, $cipherMethod, $salt, 0, $encIv). "::" . bin2hex($encIv);
        } elseif ($action === 'decrypt') {
            if (!strpos($parameters['row'], '::')) {
                throw new Warning('Are you sure what this row was encrypt early?');
            }

            list($cryptedToken, $encIv) = explode("::",$parameters['row']);
            $data = openssl_decrypt($cryptedToken, $cipherMethod, $salt, 0, hex2bin($encIv));
        } else {
            throw new BadRequestHttpException('Incorrect action type!');
        }
    
        $alertMessage = new ResponseMessage();
        $alertMessage->addInfo($action, $data);
        
        return $alertMessage;
    }
}