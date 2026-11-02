<?php

namespace App\Repository;

use App\Entity\Profesor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profesor>
 *
 * @method Profesor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profesor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profesor[]    findAll()
 * @method Profesor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profesor::class);
    }

    /**
     * @return Profesor[]
     */
    public function findAllOrderedWithAsignaciones(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.asignaciones', 'a')
            ->addSelect('a')
            ->leftJoin('a.curso', 'c')
            ->addSelect('c')
            ->leftJoin('a.division', 'd')
            ->addSelect('d')
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->addOrderBy('c.numero', 'ASC')
            ->addOrderBy('d.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneActiveByDni(string $dni): ?Profesor
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dni = :dni')
            ->andWhere('p.isActive = :active')
            ->setParameter('dni', $dni)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneWithAsignaciones(int $id): ?Profesor
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.asignaciones', 'a')
            ->addSelect('a')
            ->leftJoin('a.curso', 'c')
            ->addSelect('c')
            ->leftJoin('a.division', 'd')
            ->addSelect('d')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('c.numero', 'ASC')
            ->addOrderBy('d.numero', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
