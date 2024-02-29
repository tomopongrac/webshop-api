<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomoPongrac\WebshopApiBundle\Entity\TaxCategory;
use TomoPongrac\WebshopApiBundle\Factory\TaxCategoryFactory;
use Zenstruck\Foundry\Proxy;

#[AsCommand(name: 'webshop-api:seed-tax-categories')]
class SeedTaxCategoriesCommand extends Command
{
    public const TAX_CATEGORIES_COUNT = 5;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seeds the TaxCategory entity into the database.')
            ->setHelp('This command allows you to seed the TaxCategory entity into the database...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding tax categories...');

        for ($i = 0; $i < self::TAX_CATEGORIES_COUNT; ++$i) {
            /** @var Proxy<TaxCategory> $proxyTaxCategory */
            $proxyTaxCategory = TaxCategoryFactory::createOne();

            $this->entityManager->persist($proxyTaxCategory->object());
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error('An error occurred while seeding tax categories: '.$e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Tax categories have been successfully seeded.');

        return Command::SUCCESS;
    }
}
