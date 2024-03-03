<?php

namespace TomoPongrac\WebshopApiBundle\Factory;

use TomoPongrac\WebshopApiBundle\Entity\TotalDiscount;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TotalDiscount>
 *
 * @method        TotalDiscount|Proxy                     create(array|callable $attributes = [])
 * @method static TotalDiscount|Proxy                     createOne(array $attributes = [])
 * @method static TotalDiscount|Proxy                     find(object|array|mixed $criteria)
 * @method static TotalDiscount|Proxy                     findOrCreate(array $attributes)
 * @method static TotalDiscount|Proxy                     first(string $sortedField = 'id')
 * @method static TotalDiscount|Proxy                     last(string $sortedField = 'id')
 * @method static TotalDiscount|Proxy                     random(array $attributes = [])
 * @method static TotalDiscount|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TotalDiscountRepository|RepositoryProxy repository()
 * @method static TotalDiscount[]|Proxy[]                 all()
 * @method static TotalDiscount[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TotalDiscount[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static TotalDiscount[]|Proxy[]                 findBy(array $attributes)
 * @method static TotalDiscount[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TotalDiscount[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TotalDiscountFactory extends ModelFactory
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
            'totalprice' => self::faker()->numberBetween(1_00, 1000_00),
            'discountRate' => self::faker()->randomFloat(2, 0, 35),
        ];
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
        return TotalDiscount::class;
    }
}
