<?php

namespace TomoPongrac\WebshopApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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

        $queryParameters = [];
        $totalCountSql = 'SELECT COUNT(*) as count FROM (
        SELECT
            p.id,
            COALESCE(MIN(plp.price), p.price) AS min_price
        FROM
            product p
        LEFT JOIN price_list_product plp ON p.id = plp.product_id';

        if (0 !== count($filterProductsRequest->getFilters()->getCategories())) {
            $totalCountSql .= ' LEFT JOIN product_category pc ON p.id = pc.product_id';
        }

        $totalCountSql .= ' WHERE p.published_at IS NOT NULL';

        if (null !== $filterProductsRequest->getFilters()->getName() && '' !== $filterProductsRequest->getFilters(
        )->getName()) {
            $totalCountSql .= ' AND p.name LIKE :name';
            $queryParameters['name'] = '%'.$filterProductsRequest->getFilters()->getName().'%';
        }

        if (0 !== count($filterProductsRequest->getFilters()->getCategories())) {
            $totalCountSql .= ' AND pc.category_id IN (:categoryIds)';
            $queryParameters['categoryIds'] = $filterProductsRequest->getFilters()->getCategories();
        }

        $totalCountSql .= ' GROUP BY p.id';

        if (null !== $filterProductsRequest->getFilters()->getPrice()) {
            if (null !== $filterProductsRequest->getFilters()->getPrice()->getMin() || null !== $filterProductsRequest->getFilters()->getPrice()->getMax()) {
                $totalCountSql .= ' HAVING ';
                $havingQuery = [];
                if (null !== $filterProductsRequest->getFilters()->getPrice()->getMin()) {
                    $havingQuery[] = 'min_price >= :minPrice';
                    $queryParameters['minPrice'] = $filterProductsRequest->getFilters()->getPrice()->getMin();
                }
                if (null !== $filterProductsRequest->getFilters()->getPrice()->getMax()) {
                    $havingQuery[] = 'min_price <= :maxPrice';
                    $queryParameters['maxPrice'] = $filterProductsRequest->getFilters()->getPrice()->getMax();
                }
                $totalCountSql .= implode(' AND ', $havingQuery);
            }
        }

        $totalCountSql .= ') as total';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addScalarResult('count', 'count');
        $nativeQuery = $this->getEntityManager()->createNativeQuery($totalCountSql, $rsm);

        if (0 !== count($queryParameters)) {
            foreach ($queryParameters as $key => $value) {
                $nativeQuery->setParameter($key, $value);
            }
        }

        /** @var int $totalResults */
        $totalResults = $nativeQuery->getSingleScalarResult();

        // Get the products
        $productsQuery = $this->createQueryBuilder('p')
            ->addSelect('COALESCE(MIN(plp.price), p.price) AS min_price')
            ->leftJoin('p.priceListProducts', 'plp')
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

        if (null !== $filterProductsRequest->getFilters()->getPrice() && null !== $filterProductsRequest->getFilters(
        )->getPrice()->getMin()) {
            $productsQuery->andHaving('min_price >= :minPrice')
                ->setParameter('minPrice', $filterProductsRequest->getFilters()->getPrice()->getMin());
        }

        if (null !== $filterProductsRequest->getFilters()->getPrice() && null !== $filterProductsRequest->getFilters(
        )->getPrice()->getMax()) {
            $productsQuery->andHaving('min_price <= :maxPrice')
                ->setParameter('maxPrice', $filterProductsRequest->getFilters()->getPrice()->getMax());
        }

        $productsQuery->groupBy('p.id');

        if (null !== $filterProductsRequest->getOrder()->getBy() && null !== $filterProductsRequest->getOrder()->getDirection()) {
            $productsQuery->orderBy('p.'.$filterProductsRequest->getOrder()->getBy(), $filterProductsRequest->getOrder()->getDirection());
        }

        /** @var Product[] $products */
        $products = $productsQuery->setFirstResult($offset)
            ->setMaxResults($filterProductsRequest->getPagination()->getLimit())
            ->getQuery()
            ->getResult();

        $productsForReturn = [];
        foreach ($products as $product) {
            if (isset($product['min_price']) && isset($product[0])) {
                $product[0]->setPrice($product['min_price']);
                $productsForReturn[] = $product[0];
            }
        }

        return [$productsForReturn, $totalResults];
    }
}
