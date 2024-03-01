<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomoPongrac\WebshopApiBundle\Entity\PriceList;
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Factory\PriceListFactory;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;
use Zenstruck\Foundry\Proxy;

#[AsCommand(name: 'webshop-api:seed-price-list-product')]
class SeedPriceListProductCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seeds the PriceListProduct entity into the database.')
            ->setHelp('This command allows you to seed the PriceListProduct entity into the database...')
            ->addArgument('numberOfPriceLists', InputArgument::REQUIRED, 'Number of price list products to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding price lists...');

        if (!is_numeric($input->getArgument('numberOfPriceLists')) || $input->getArgument('numberOfPriceLists') < 1) {
            $io->error('Number of price lists must be a positive number.');

            return Command::FAILURE;
        }

        $numberOfPriceLists = (int) $input->getArgument('numberOfPriceLists');

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        $products = $productRepository->findRandomProducts(2000);

        $progressBar = $io->createProgressBar($numberOfPriceLists);

        $batchSize = 20;
        for ($i = 0; $i < $numberOfPriceLists; ++$i) {
            /** @var Proxy<PriceList> $proxyPriceList */
            $proxyPriceList = PriceListFactory::createOne();
            $priceList = $proxyPriceList->object();

            $this->entityManager->persist($priceList);

            $connection = $this->entityManager->getConnection();

            foreach ($products as $product) {
                // random price (1 - 1000
                $price = Factory::create()->randomFloat(2, 1, 1000);

                $sql = 'INSERT INTO price_list_product (price_list_id, product_id, price, created_at) VALUES (:price_list_id, :product_id, :price, NOW())';
                $statement = $connection->prepare($sql);
                $statement->bindValue('price_list_id', $priceList->getId());
                $statement->bindValue('product_id', $product['id']);
                $statement->bindValue('price', $price);
                $statement->executeQuery();
            }

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
            unset($priceList);
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
