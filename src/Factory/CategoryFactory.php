<?php

namespace TomoPongrac\WebshopApiBundle\Factory;

use TomoPongrac\WebshopApiBundle\Entity\Category;
use TomoPongrac\WebshopApiBundle\Repository\CategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Category>
 *
 * @method        Category|Proxy                     create(array|callable $attributes = [])
 * @method static Category|Proxy                     createOne(array $attributes = [])
 * @method static Category|Proxy                     find(object|array|mixed $criteria)
 * @method static Category|Proxy                     findOrCreate(array $attributes)
 * @method static Category|Proxy                     first(string $sortedField = 'id')
 * @method static Category|Proxy                     last(string $sortedField = 'id')
 * @method static Category|Proxy                     random(array $attributes = [])
 * @method static Category|Proxy                     randomOrCreate(array $attributes = [])
 * @method static CategoryRepository|RepositoryProxy repository()
 * @method static Category[]|Proxy[]                 all()
 * @method static Category[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Category[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Category[]|Proxy[]                 findBy(array $attributes)
 * @method static Category[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Category[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class CategoryFactory extends ModelFactory
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
            'description' => self::faker()->text(180),
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
        return Category::class;
    }
}
