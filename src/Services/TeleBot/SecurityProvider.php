<?php


namespace App\Services\TeleBot;

use App\Repository\UserRepository;
use App\Services\TeleBot\Exception\UnauthorisedUserException;
use App\Services\TeleBot\Exception\UnknownUserException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityProvider
{
    private $session;
    private $userRepo;
    private $coder;

    public function __construct(
        UserRepository $userRepo,
        SessionInterface $session,
        UserPasswordEncoderInterface $coder
    ) {
        $this->userRepo = $userRepo;
        $this->session = $session;
        $this->coder = $coder;
    }

    public function checkUser(int $userId) {
        if (!$this->session->has(md5($userId))) {
            throw new UnauthorisedUserException('User is unauthorised, please login');
        }
    }

    public function login(int $userId, string $name, string $password) {
        if (!$user = $this->userRepo->findByName($name)) {
            throw new UnknownUserException('Unknown user!');
        }

        if (!$this->coder->isPasswordValid($user, $password)) {
            throw new UnauthorisedUserException('User is unauthorised, please login');
        }

        $this->session->set(md5($userId), 1);
    }
}