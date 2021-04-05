<?php


namespace App\Tests\Services\Telebot;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\TeleBot\SecurityProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class SecurityProviderTest extends KernelTestCase
{
    /**
     * @doesNotPerformAssertions
     * @throws \App\Services\TeleBot\Exception\UnauthorisedUserException
     * @throws \App\Services\TeleBot\Exception\UnknownUserException
     */
    public function testLogin(){
        self::bootKernel();
        $container = self::$container;
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByName')->willReturn((new User())->setName('turbo')->setPassword('1234'));
        $coder = $this->createMock(UserPasswordEncoder::class);
        $coder->method('isPasswordValid')->willReturn(true);
        $session = $container->get('session');
        $obj = new SecurityProvider(
            $repo,
            $session,
            $coder);

        $obj->login(1,'turbo', '1234');
        $obj->checkUser(1);
    }
}