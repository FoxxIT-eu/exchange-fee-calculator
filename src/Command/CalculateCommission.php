<?php

namespace App\Command;

use App\Dto\ExchangeRatesDto;
use App\Services\BinCheckerService;
use App\Services\ExchangeRatesService;
use App\Util\CommissionCalculator;
use App\Util\TransactionsTxtFileReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:calculate-commission',
    aliases: ['app:calculate']
)]
class CalculateCommission extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Calculates fees for transactions')
            ->setHelp('Requires txt file with transactions list')
            ->addArgument('filepath', InputArgument::REQUIRED, 'Path to file with transactions list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $section = $output->section();

        $section->writeln([
            'Commission Calculator',
        ]);
        $filePath = $input->getArgument('filepath');
        try {
            $section->writeln([
                'Connecting to API\'s',
            ]);
            $reader = new TransactionsTxtFileReader($filePath);
            $binChecker = new BinCheckerService(HttpClient::create());
            $exchangeRates = new ExchangeRatesService(HttpClient::create());
            $calculator = new CommissionCalculator(
                $binChecker,
                $exchangeRates
            );
        } catch (\Throwable $exception) {
            $section->writeln([
                $exception->getMessage(),
            ]);
            return Command::FAILURE;
        }
        $section->clear();
        while ($reader->haveRows()) {
            try {
                $section->writeln([
                    $calculator->calculate($reader->getNextRow()),
                ]);
            } catch (\Throwable $exception) {
                $section->writeln([
                    'Calculation error: ' . $exception->getMessage()
                ]);
            }

        }
        return Command::SUCCESS;
    }
}