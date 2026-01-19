<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function findConversation(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(m.sender = :user1 AND m.recipient = :user2) OR (m.sender = :user2 AND m.recipient = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLastMessages(User $user): array
    {
        // Simple distinct users the user has conversed with
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT DISTINCT 
                CASE 
                    WHEN sender_id = :userId THEN recipient_id 
                    ELSE sender_id 
                END as other_user_id
            FROM message
            WHERE sender_id = :userId OR recipient_id = :userId
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['userId' => $user->getId()]);

        return $result->fetchAllAssociative();
    }

    public function countUnread(User $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.recipient = :user')
            ->andWhere('m.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
