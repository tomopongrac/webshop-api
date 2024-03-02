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
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

#[AsCommand(name: 'webshop-api:seed-contract-list-product')]
class SeedContractListProductCommand extends Command
{
    public function __construct(readonly private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seeds the Contract List Product entity into the database...')
            ->setHelp('This command allows you to seed the ContractListProduct entity into the database...')
            ->addArgument('numberOfUsers', InputArgument::REQUIRED, 'Number of users to import')
            ->addArgument('userClass', InputArgument::OPTIONAL, 'User class to use', 'App\Entity\User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding contract list users...');

        if (!is_numeric($input->getArgument('numberOfUsers')) || $input->getArgument('numberOfUsers') < 1) {
            $io->error('Number of users must be a positive number.');

            return Command::FAILURE;
        }

        $userClass = $input->getArgument('userClass');
        if (!is_string($userClass)) {
            $output->writeln('The user class must be a string.');

            return Command::INVALID;
        }

        if (!class_exists($userClass) || !in_array(UserWebShopApiInterface::class, class_implements($userClass), true)) {
            $output->writeln(sprintf('The class "%s" does not exist or does not implement UserWebShopApiInterface.', $userClass));

            return Command::INVALID;
        }

        $numberOfUsers = (int) $input->getArgument('numberOfUsers');

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        $products = $productRepository->findRandomProducts(200);

        $connection = $this->entityManager->getConnection();

        $progressBar = $io->createProgressBar($numberOfUsers);
        $batchSize = 20;
        for ($i = 0; $i < $numberOfUsers; ++$i) {
            /** @var UserWebShopApiInterface $user */
            $user = new $userClass();
            $user->setEmail(sprintf('john.doe%s@example.com', $i));
            $user->setPassword('$2y$13$JpW4jaAVJ44zo0UYT/r.xus0WXITtAJPi.0JaB4miRIMocDsFDd6m'); // password is string 'password'

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            foreach ($products as $product) {
                // random price (1 - 1000)
                $price = Factory::create()->randomFloat(2, 1, 1000);

                $sql = 'INSERT INTO contract_list_product (user_id, product_id, price, created_at) VALUES (:user_id, :product_id, :price, NOW())';
                $statement = $connection->prepare($sql);
                $statement->bindValue('user_id', $user->getId());
                $statement->bindValue('product_id', $product['id']);
                $statement->bindValue('price', $price);
                $statement->executeQuery();
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
