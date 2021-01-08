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
    /**
     * @param $key
     *
     * @return mixed
     * @throws
     */
    public function getXmlBodyByKey($key)
    {
        $query = $this->_em->createQuery('SELECT t.xml FROM ' . TestXml::class . ' t WHERE t.hash LIKE :hash');
        $query->setParameter('hash', "$key");
        $xml =  $query->getResult();
    
        if (!$xml) {
            throw new BadRequestHttpException('Incorrect or expired key');
        }
        
        return reset($xml)['xml'];
    }
    
    /**
     * @return string
     */
    protected function getEntityName()
    {
        return TestXml::class;
    }
}
