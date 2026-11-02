<?php

namespace App\Repository;

use App\Entity\Justificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Justificacion>
 *
 * @method Justificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Justificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Justificacion[]    findAll()
 * @method Justificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JustificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Justificacion::class);
    }

    /**
     * @return Justificacion[]
     */
    public function findByFecha(\DateTimeInterface $fecha): array
    {
        return $this->createQueryBuilder('j')
            ->innerJoin('j.profesor', 'p')
            ->addSelect('p')
            ->andWhere('j.fechaAfectada = :fecha')
            ->setParameter('fecha', $fecha)
            ->getQuery()
            ->getResult();
    }
}
