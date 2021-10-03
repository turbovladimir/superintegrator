<?php
namespace App\Tests\functional;

use App\Entity\TelebotKey;
use App\Repository\TelebotKeyRepository;

class KeyRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\FunctionalTester
     */
    protected $tester;
    /**
     * @var TelebotKeyRepository
     */
    private $repo;

    protected function _before()
    {
        parent::_before();
        $key = (new TelebotKey())->setName('service')->setValue('value')->setAddedAt(new \DateTime())->setUserId(1);
        $this->tester->haveInRepository($key);
        $this->repo = $this->tester->grabRepository(TelebotKeyRepository::class);
    }

    public function testFetchByUserIdAndNameLike() {
        $key = $this->repo->fetchKeyByUserIdAndServiceName(1, 'ser');
        $this->assertSame('service', $key->getName());
    }


    public function testUpdate() {
        $keys = $this->repo->findAll();
        $key = reset($keys);
        $key->setName('serviceRename');
        $em = $this->tester->grabService('doctrine.orm.entity_manager');
        $em->persist($key);
        $em->flush();
        $this->assertEquals('serviceRename', $key->getName());
        $this->tester->seeInRepository(TelebotKey::class, ['name' => 'serviceRename']);
        $this->tester->dontSeeInRepository(TelebotKey::class, ['name' => 'service']);
    }
}