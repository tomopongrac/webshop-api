<?php

namespace TomoPongrac\WebshopApiBundle\Factory;

use TomoPongrac\WebshopApiBundle\Entity\ContractListProduct;
use Zenstruck\Foundry\LazyValue;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ContractListProduct>
 *
 * @method        ContractListProduct|Proxy                     create(array|callable $attributes = [])
 * @method static ContractListProduct|Proxy                     createOne(array $attributes = [])
 * @method static ContractListProduct|Proxy                     find(object|array|mixed $criteria)
 * @method static ContractListProduct|Proxy                     findOrCreate(array $attributes)
 * @method static ContractListProduct|Proxy                     first(string $sortedField = 'id')
 * @method static ContractListProduct|Proxy                     last(string $sortedField = 'id')
 * @method static ContractListProduct|Proxy                     random(array $attributes = [])
 * @method static ContractListProduct|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ContractListProductRepository|RepositoryProxy repository()
 * @method static ContractListProduct[]|Proxy[]                 all()
 * @method static ContractListProduct[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ContractListProduct[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ContractListProduct[]|Proxy[]                 findBy(array $attributes)
 * @method static ContractListProduct[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ContractListProduct[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ContractListProductFactory extends ModelFactory
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
        return ContractListProduct::class;
    }
}
