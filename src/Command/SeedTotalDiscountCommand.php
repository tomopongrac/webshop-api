<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomoPongrac\WebshopApiBundle\Entity\TotalDiscount;

#[AsCommand(name: 'webshop-api:seed-total-discount')]
class SeedTotalDiscountCommand extends Command
{
    public function __construct(readonly private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Seeds the default values for TotalDiscount entity into the database...')
            ->setHelp('This command allows you to seed the TotalDiscount entity into the database...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding total discount...');

        $connection = $this->entityManager->getConnection();

        $totalDiscounts = [
            ['totalPrice' => 10000, 'discount' => 0.1],
            ['totalPrice' => 50000, 'discount' => 0.15],
        ];

        foreach ($totalDiscounts as $totalDiscount) {
            $totalDiscount = (new TotalDiscount())
                ->setTotalPrice($totalDiscount['totalPrice'])
                ->setDiscountRate($totalDiscount['discount']);

            $this->entityManager->persist($totalDiscount);
            $this->entityManager->flush();
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error('An error occurred while seeding total discount: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
