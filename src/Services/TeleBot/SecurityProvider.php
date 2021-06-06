<?php


namespace App\Services\TeleBot;

use App\Repository\UserRoleRepository;
use App\Services\TeleBot\Exception\UnauthorisedUserException;
use App\Services\TeleBot\Exception\UnknownUserException;
use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityProvider
{
    private $cache;
    private $userRepo;
    private $coder;

    public function __construct(
        UserRoleRepository $userRepo,
        FilesystemCache $fileCache,
        UserPasswordEncoderInterface $coder
    ) {
        $this->userRepo = $userRepo;
        $this->cache = $fileCache;
        $this->coder = $coder;
    }

    public function checkUser(int $userId) {
        if (!$this->cache->contains(md5($userId))) {
            throw new UnauthorisedUserException('User is unauthorised.');
        }
    }

    public function login(int $userId, string $name, string $password) {
        if (!$user = $this->userRepo->findByName($name)) {
            throw new UnknownUserException('Unknown user!');
        }

        if (!$this->coder->isPasswordValid($user, $password)) {
            throw new UnauthorisedUserException('User is unauthorised.');
        }

        $this->cache->save(md5($userId), 1, 24 * 3600);
    }
}