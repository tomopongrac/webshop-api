<?php

namespace TomoPongrac\WebshopApiBundle\Factory;

use TomoPongrac\WebshopApiBundle\Entity\PriceListProduct;
use Zenstruck\Foundry\LazyValue;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<PriceListProduct>
 *
 * @method        PriceListProduct|Proxy                     create(array|callable $attributes = [])
 * @method static PriceListProduct|Proxy                     createOne(array $attributes = [])
 * @method static PriceListProduct|Proxy                     find(object|array|mixed $criteria)
 * @method static PriceListProduct|Proxy                     findOrCreate(array $attributes)
 * @method static PriceListProduct|Proxy                     first(string $sortedField = 'id')
 * @method static PriceListProduct|Proxy                     last(string $sortedField = 'id')
 * @method static PriceListProduct|Proxy                     random(array $attributes = [])
 * @method static PriceListProduct|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PriceListProductRepository|RepositoryProxy repository()
 * @method static PriceListProduct[]|Proxy[]                 all()
 * @method static PriceListProduct[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static PriceListProduct[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static PriceListProduct[]|Proxy[]                 findBy(array $attributes)
 * @method static PriceListProduct[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PriceListProduct[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PriceListProductFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'priceList' => LazyValue::new(fn () => PriceListFactory::createOne()),
            'product' => LazyValue::new(fn () => ProductFactory::createOne()),        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }

    protected static function getClass(): string
    {
        return PriceListProduct::class;
    }
}
