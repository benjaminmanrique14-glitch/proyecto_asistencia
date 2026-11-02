<?php

namespace App\Repository;

use App\Entity\Administrador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Administrador>
 *
 * @method Administrador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Administrador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Administrador[]    findAll()
 * @method Administrador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministradorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Administrador::class);
    }
}
