<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomoPongrac\WebshopApiBundle\Entity\Category;
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Entity\TaxCategory;
use TomoPongrac\WebshopApiBundle\Factory\ProductFactory;
use Zenstruck\Foundry\Proxy;

#[AsCommand(name: 'webshop-api:seed-products')]
class SeedProductsCommand extends Command
{
    public function __construct(readonly private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seeds the Product entity into the database.')
            ->setHelp('This command allows you to seed the Product entity into the database...')
            ->addArgument('numberOfProducts', InputArgument::REQUIRED, 'Number of products to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln('Seeding products...');

        if (!is_numeric($input->getArgument('numberOfProducts')) || $input->getArgument('numberOfProducts') < 1) {
            $io->error('Number of products must be a positive number.');

            return Command::FAILURE;
        }

        $numberOfProducts = (int) $input->getArgument('numberOfProducts');

        // get all categories
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $taxCategories = $this->entityManager->getRepository(TaxCategory::class)->findAll();
        $taxCategory = $taxCategories[array_rand($taxCategories)];

        $progressBar = $io->createProgressBar($numberOfProducts);

        $batchSize = 5;
        for ($i = 0; $i < $numberOfProducts; ++$i) {
            /** @var Proxy<Product> $proxyProduct */
            $proxyProduct = ProductFactory::createOne([
                'categories' => [$categories[array_rand($categories)]],
                'taxCategory' => $taxCategory,
                'publishedAt' => 4 !== random_int(0, 4) ? new \DateTimeImmutable() : null,
            ]);

            $this->entityManager->persist($proxyProduct->object());

            if (($i % $batchSize) === 0) {
                try {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    $taxCategory = $this->entityManager->getRepository(TaxCategory::class)->find($taxCategories[array_rand($taxCategories)]->getId());
                } catch (\Exception $e) {
                    $io->error('An error occurred while seeding products: '.$e->getMessage());

                    return Command::FAILURE;
                }
            }

            $progressBar->advance();
            gc_collect_cycles();
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error('An error occurred while seeding categories: '.$e->getMessage());

            return Command::FAILURE;
        }

        $progressBar->finish();

        $io->success('Products have been successfully seeded.');

        return Command::SUCCESS;
    }
}
