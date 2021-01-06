<?php


namespace App\Services\Tools;


use App\Response\AlertMessage;
use App\Services\AbstractService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Cryptor extends AbstractService
{
    public function processRequest(Request $request) : AlertMessage {
        parse_str($request->getContent(), $parameters);
        
        if (
            empty($parameters['cryptor_row']) ||
            empty($parameters['cryptor_salt']) ||
            empty($parameters['action'])
        ) {
            throw new BadRequestHttpException('All fields needs to be filled!');
        }
        
        $action = $parameters['action'];
        $salt = $parameters['cryptor_salt'];
        $cipherMethod = 'aes-128-ctr';
        
        if ($action === 'encrypt') {
            $token = $parameters['cryptor_row'];
            $encIv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod));
            $data = openssl_encrypt($token, $cipherMethod, $salt, 0, $encIv). "::" . bin2hex($encIv);
        } elseif ($action === 'decrypt') {
            list($cryptedToken, $encIv) = explode("::",$parameters['cryptor_row']);
            $data = openssl_decrypt($cryptedToken, $cipherMethod, $salt, 0, hex2bin($encIv));
        } else {
            throw new BadRequestHttpException('Incorrect action type!');
        }
    
        $alertMessage = new AlertMessage();
        $alertMessage->addAlert($action, $data);
        
        return $alertMessage;
    }
}