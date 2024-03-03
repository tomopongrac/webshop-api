<?php

namespace TomoPongrac\WebshopApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TomoPongrac\WebshopApiBundle\DTO\FilterProductsRequest;
use TomoPongrac\WebshopApiBundle\DTO\ListProductsQueryParameters;
use TomoPongrac\WebshopApiBundle\Entity\Category;
use TomoPongrac\WebshopApiBundle\Entity\PriceListProduct;
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Service\ModifyPricesForProductsService;

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
    public function __construct(
        ManagerRegistry $registry,
        private readonly ContractListProductRepository $contractListProductRepository,
        private readonly ModifyPricesForProductsService $modifyPricesForProductsService,
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findProductsByIds(array $ids, ?UserWebShopApiInterface $user = null): array
    {
        /** @var Product[] $products */
        $products = $this->createQueryBuilder('p')
            ->andWhere('p.id IN(:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $this->modifyPricesForProductsService->modifyPricesForProducts($products, $user);

        return $products;
    }

    public function findRandomProducts(int $limit): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM product ORDER BY RAND() LIMIT '.$limit;
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getSingleProduct(int $productId, ?UserWebShopApiInterface $user = null): ?Product
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

            // check if there is a contract price for the user
            if (null !== $user) {
                $contractPrice = $this->contractListProductRepository->findContractPriceListForUserAndProduct($user, $product);
                if (null !== $contractPrice) {
                    $prices[] = $contractPrice->getPrice();
                }
            }

            // Check if there are any prices before calculating the minimum
            if (count($prices) > 0) {
                $min_price = min($prices);
                $product->setPrice($min_price);
            }
        }

        return $product;
    }

    public function getProducts(ListProductsQueryParameters $queryParameters, ?UserWebShopApiInterface $user = null, ?Category $category = null): array
    {
        $offset = ((int) $queryParameters->getPage() - 1) * (int) $queryParameters->getLimit();

        // Get the total products
        $totalResultsQuery = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.publishedAt IS NOT NULL');

        if (null !== $category) {
            $totalResultsQuery->leftJoin('p.categories', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        $totalResultsQuery->getQuery();

        /** @var int $totalResults */
        $totalResults = $totalResultsQuery->getQuery()->getSingleScalarResult();

        // Get the products
        $productsQuery = $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL');

        if (null !== $category) {
            $productsQuery->leftJoin('p.categories', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        /** @var Product[] $products */
        $products = $productsQuery->setFirstResult($offset)
            ->setMaxResults((int) $queryParameters->getLimit())
            ->getQuery()
            ->getResult();

        // Get the product IDs
        $productIds = array_map(function ($product) {
            return $product->getId();
        }, $products);

        // Get priceListProducts for these products
        /** @var PriceListProduct[] $priceListProducts */
        $priceListProducts = $this->createQueryBuilder('p')
            ->select('p, plp')
            ->leftJoin('p.priceListProducts', 'plp')
            ->andWhere('p.id IN (:productIds)')
            ->setParameter('productIds', $productIds)
            ->getQuery()
            ->getResult();

        // If the product was found, proceed with the minimum price
        if (0 !== count($priceListProducts)) {
            /** @var Product $product */
            foreach ($priceListProducts as $product) {
                // Retrieve all prices from priceListProducts
                $prices = array_map(function ($priceListProduct) {
                    return $priceListProduct->getPrice();
                }, $product->getPriceListProducts()->toArray());

                // check if there is a contract price for the user
                if (null !== $user) {
                    $contractPrice = $this->contractListProductRepository->findContractPriceListForUserAndProduct(
                        $user,
                        $product
                    );
                    if (null !== $contractPrice) {
                        $prices[] = $contractPrice->getPrice();
                    }
                }

                // Check if there are any prices before calculating the minimum
                if (count($prices) > 0) {
                    $min_price = min($prices);
                    $product->setPrice($min_price);
                }
            }
        }

        return [$priceListProducts, $totalResults];
    }

    public function filterProducts(FilterProductsRequest $filterProductsRequest, ?UserWebShopApiInterface $user = null): array
    {
        $offset = ($filterProductsRequest->getPagination()->getPage() - 1) * $filterProductsRequest->getPagination()->getLimit();

        // Get the total products
        $totalResultsQuery = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.publishedAt IS NOT NULL');

        if (null !== $filterProductsRequest->getFilters()->getName()) {
            $totalResultsQuery->andWhere('p.name LIKE :name')
                ->setParameter('name', '%'.$filterProductsRequest->getFilters()->getName().'%');
        }

        if (0 !== count($filterProductsRequest->getFilters()->getCategories())) {
            $totalResultsQuery->leftJoin('p.categories', 'c')
                ->andWhere('c.id IN (:categoryIds)')
                ->setParameter('categoryIds', $filterProductsRequest->getFilters()->getCategories());
        }

        $totalResultsQuery->getQuery();

        /** @var int $totalResults */
        $totalResults = $totalResultsQuery->getQuery()->getSingleScalarResult();

        // Get the products
        $productsQuery = $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL');

        if (null !== $filterProductsRequest->getFilters()->getName()) {
            $productsQuery->andWhere('p.name LIKE :name')
                ->setParameter('name', '%'.$filterProductsRequest->getFilters()->getName().'%');
        }

        if (0 !== count($filterProductsRequest->getFilters()->getCategories())) {
            $productsQuery->leftJoin('p.categories', 'c')
                ->andWhere('c.id IN (:categoryIds)')
                ->setParameter('categoryIds', $filterProductsRequest->getFilters()->getCategories());
        }

        /** @var Product[] $products */
        $products = $productsQuery->setFirstResult($offset)
            ->setMaxResults($filterProductsRequest->getPagination()->getLimit())
            ->getQuery()
            ->getResult();

        return [$products, $totalResults];
    }
}
