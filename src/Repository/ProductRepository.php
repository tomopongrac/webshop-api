<?php

namespace TomoPongrac\WebshopApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TomoPongrac\WebshopApiBundle\Entity\PriceListProduct;
use TomoPongrac\WebshopApiBundle\Entity\Product;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findRandomProducts(int $limit): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM product ORDER BY RAND() LIMIT '.$limit;
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getSingleProduct(int $productId): ?Product
    {
        // Fetching the main product with priceListProducts, categories, and taxCategory associations!
        /** @var Product|null $product */
        $product = $this->createQueryBuilder('p')
            ->select('p, plp, c, t')
            ->leftJoin('p.priceListProducts', 'plp')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.taxCategory', 't')
            ->andWhere('p.id = :productId')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult();

        // If the product was found, proceed with the minimum price
        if (null !== $product) {
            /** @var PriceListProduct[] $pricesListProducts */
            $pricesListProducts = $product->getPriceListProducts()->toArray();

            // Retrieve all prices from priceListProducts
            $prices = array_map(function ($priceListProduct) {
                return $priceListProduct->getPrice();
            }, $pricesListProducts);

            // Check if there are any prices before calculating the minimum
            if (count($prices) > 0) {
                $min_price = min($prices);
                $product->setPrice($min_price);
            }
        }

        return $product;
    }
}
