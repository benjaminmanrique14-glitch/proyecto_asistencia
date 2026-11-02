<?php

namespace App\Repository;

use App\Entity\Asistencia;
use App\Entity\Profesor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asistencia>
 *
 * @method Asistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asistencia[]    findAll()
 * @method Asistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asistencia::class);
    }

    /**
     * @return Asistencia[]
     */
    public function findByFecha(\DateTimeInterface $fecha): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.profesor', 'p')
            ->addSelect('p')
            ->andWhere('a.fecha = :fecha')
            ->setParameter('fecha', $fecha)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByProfesorAndFecha(Profesor $profesor, \DateTimeInterface $fecha): ?Asistencia
    {
        return $this->findOneBy([
            'profesor' => $profesor,
            'fecha' => $fecha,
        ]);
    }
}
