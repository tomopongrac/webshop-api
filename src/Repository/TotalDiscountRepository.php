<?php

namespace TomoPongrac\WebshopApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TomoPongrac\WebshopApiBundle\Entity\TotalDiscount;

/**
 * @extends ServiceEntityRepository<TotalDiscount>
 *
 * @method TotalDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method TotalDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method TotalDiscount[]    findAll()
 * @method TotalDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TotalDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TotalDiscount::class);
    }

    public function findTotalDiscount(int $totalPrice): ?TotalDiscount
    {
        /** @var ?TotalDiscount $totalDiscount */
        $totalDiscount = $this->createQueryBuilder('d')
            ->where('d.totalPrice <= :totalPrice')
            ->setParameter('totalPrice', $totalPrice)
            ->orderBy('d.totalPrice', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $totalDiscount;
    }
}
