<?php

namespace App\Repository;

use App\Entity\Conversation;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends BaseRepository
{
    /**
     * @param string $statusFrom
     * @param string $statusTo
     * @return int[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStatus(string $statusFrom, string $statusTo) : array {
        $openConversations = $this->findBy(['status' => $statusFrom]);
        $messageIds = [];

        foreach ($openConversations as $conversation) {
            $messageIds = array_merge($messageIds, $conversation->getMessageIdsFromHistory());
            $conversation->setStatus($statusTo);
            $this->_em->persist($conversation);
        }

        $this->_em->flush();

        return $messageIds;
    }

    public function cancelConversation(Conversation $conversation) {
        if ($conversation->getStatus() === Conversation::STATUS_CLOSED) {
            throw new \InvalidArgumentException('Conversation already canceled!');
        }

        $conversation->setStatus(Conversation::STATUS_CLOSED);
        $this->_em->persist($conversation);
        $this->_em->flush();
    }

    protected function getEntityName()
    {
        return Conversation::class;
    }
}
