<?php

namespace TomoPongrac\WebshopApiBundle\Factory;

use TomoPongrac\WebshopApiBundle\Entity\TaxCategory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TaxCategory>
 *
 * @method        TaxCategory|Proxy                     create(array|callable $attributes = [])
 * @method static TaxCategory|Proxy                     createOne(array $attributes = [])
 * @method static TaxCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static TaxCategory|Proxy                     findOrCreate(array $attributes)
 * @method static TaxCategory|Proxy                     first(string $sortedField = 'id')
 * @method static TaxCategory|Proxy                     last(string $sortedField = 'id')
 * @method static TaxCategory|Proxy                     random(array $attributes = [])
 * @method static TaxCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TaxCategoryRepository|RepositoryProxy repository()
 * @method static TaxCategory[]|Proxy[]                 all()
 * @method static TaxCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TaxCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static TaxCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static TaxCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TaxCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TaxCategoryFactory extends ModelFactory
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
            'name' => self::faker()->text(180),
            'rate' => self::faker()->randomFloat(2, 0, 35),
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
        return TaxCategory::class;
    }
}
