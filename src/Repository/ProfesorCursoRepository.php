<?php

namespace App\Repository;

use App\Entity\ProfesorCurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfesorCurso>
 *
 * @method ProfesorCurso|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfesorCurso|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfesorCurso[]    findAll()
 * @method ProfesorCurso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesorCursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfesorCurso::class);
    }
}
