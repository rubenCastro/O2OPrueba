<?php

namespace App\Repository;

use App\Entity\FoodPairing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoodPairing|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodPairing|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodPairing[]    findAll()
 * @method FoodPairing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodPairingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodPairing::class);
    }

    // /**
    //  * @return FoodPairing[] Returns an array of FoodPairing objects
    //  */
    
    public function findByNameField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?FoodPairing
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
