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
use TomoPongrac\WebshopApiBundle\Factory\CategoryFactory;
use Zenstruck\Foundry\Proxy;

#[AsCommand(name: 'webshop-api:seed-categories')]
class SeedCategoriesCommand extends Command
{
    public function __construct(readonly private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('webshop-api:seed-categories')
            ->setDescription('Seeds the Category entity into the database.')
            ->setHelp('This command allows you to seed the Category entity into the database...')
            ->addArgument('numberOfCategories', InputArgument::REQUIRED, 'Number of categories to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding categories...');

        if (!is_numeric($input->getArgument('numberOfCategories')) || $input->getArgument('numberOfCategories') < 1) {
            $io->error('Number of categories must be a positive number.');

            return Command::FAILURE;
        }

        $numberOfCategories = (int) $input->getArgument('numberOfCategories');

        $progressBar = $io->createProgressBar($numberOfCategories);
        $batchSize = 20;
        for ($i = 0; $i < $numberOfCategories; ++$i) {
            /** @var Proxy<Category> $proxyCategory */
            $proxyCategory = CategoryFactory::new()->create();
            $this->entityManager->persist($proxyCategory->object());

            if (($i % $batchSize) === 0) {
                try {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                } catch (\Exception $e) {
                    $io->error('An error occurred while seeding categories: '.$e->getMessage());

                    return Command::FAILURE;
                }
            }

            $progressBar->advance();
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error('An error occurred while seeding categories: '.$e->getMessage());

            return Command::FAILURE;
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
