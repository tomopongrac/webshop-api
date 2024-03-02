<?php

namespace TomoPongrac\WebshopApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TomoPongrac\WebshopApiBundle\Entity\ContractListProduct;
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;

/**
 * @extends ServiceEntityRepository<ContractListProduct>
 *
 * @method ContractListProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContractListProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContractListProduct[]    findAll()
 * @method ContractListProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractListProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContractListProduct::class);
    }

    public function findContractPriceListForUserAndProduct(UserWebShopApiInterface $user, Product $product): ?ContractListProduct
    {
        /** @var ?ContractListProduct $contractPriceList */
        $contractPriceList = $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.product = :product')
            ->setParameter('user', $user)
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();

        return $contractPriceList;
    }
}
