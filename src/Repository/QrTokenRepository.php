<?php

namespace App\Repository;

use App\Entity\QrToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QrToken>
 *
 * @method QrToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method QrToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method QrToken[]    findAll()
 * @method QrToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QrTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QrToken::class);
    }

    public function findValidByToken(string $token): ?QrToken
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.profesor', 'p')
            ->addSelect('p')
            ->andWhere('q.token = :token')
            ->andWhere('q.isUsed = :used')
            ->andWhere('q.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('used', false)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
