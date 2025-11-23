<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Checkout\CheckoutServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:checkout',
    description: 'Add a short description for your command',
)]
class CheckoutCommand extends Command
{
    public function __construct(
        private readonly CheckoutServiceInterface $checkoutService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('items', InputArgument::REQUIRED, 'String like "AABAC"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $items = (string) $input->getArgument('items');

        try {
            $result = $this->checkoutService->checkout($items);
        } catch (Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $output->writeln(\sprintf('Sale ID: %s', $result['sale']->getPublicId()));
        foreach ($result['lineDetails'] as $line) {
            $promoText = '';
            if (!empty($line['appliedPromotions'])) {
                $promoText = ' [' . implode(', ', $line['appliedPromotions']) . ']';
            }

            $output->writeln(\sprintf(
                '%s x%d -> %.2f%s',
                $line['sku'],
                $line['quantity'],
                $line['linePrice'] / 100,
                $promoText
            ));
        }

        $output->writeln(\sprintf('TOTAL: %.2f', $result['totalPrice'] / 100));

        return Command::SUCCESS;
    }
}
