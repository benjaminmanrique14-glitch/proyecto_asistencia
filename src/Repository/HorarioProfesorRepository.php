<?php

namespace App\Repository;

use App\Entity\HorarioProfesor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HorarioProfesor>
 *
 * @method HorarioProfesor|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorarioProfesor|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorarioProfesor[]    findAll()
 * @method HorarioProfesor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioProfesorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HorarioProfesor::class);
    }

    /**
     * @return HorarioProfesor[]
     */
    public function findByDiaSemana(int $diaSemana): array
    {
        return $this->createQueryBuilder('h')
            ->innerJoin('h.profesor', 'p')
            ->addSelect('p')
            ->innerJoin('h.profesorCurso', 'pc')
            ->addSelect('pc')
            ->innerJoin('pc.curso', 'c')
            ->addSelect('c')
            ->innerJoin('pc.division', 'd')
            ->addSelect('d')
            ->andWhere('h.diaSemana = :dia')
            ->andWhere('p.isActive = :active')
            ->setParameter('dia', $diaSemana)
            ->setParameter('active', true)
            ->orderBy('c.numero', 'ASC')
            ->addOrderBy('d.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
