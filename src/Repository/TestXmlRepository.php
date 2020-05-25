<?php

namespace App\Repository;

use App\Entity\Superintegrator\TestXml;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method TestXml|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestXml|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestXml[]    findAll()
 * @method TestXml[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestXmlRepository extends BaseRepository
{
    protected $entity = TestXml::class;
    
    /**
     * @param $sourceName
     * @param $logs
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveLog($sourceName, $logs)
    {
        foreach ($logs as $log) {
            $TestXml = new TestXml();
            $TestXml->setSource($sourceName);
            $TestXml->setLogData($log);
            $this->_em->persist($TestXml);
        }
        
        $this->_em->flush();
    }
    
    /**
     * @param $key
     *
     * @return mixed
     * @throws
     */
    public function getXmlBodyByKey($key)
    {
        $query = $this->_em->createQuery('SELECT t.xml FROM ' . TestXml::class . ' t WHERE t.url LIKE :word');
        $query->setParameter('word', "%{$key}%");
        $xml =  $query->getResult();
    
        if (!$xml) {
            throw new BadRequestHttpException('Incorrect or expired key');
        }
        
        return reset($xml)['xml'];
    }
}
