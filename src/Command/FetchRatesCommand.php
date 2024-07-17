<?php

namespace App\Command;

use App\Enum\BaseCurrencyEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\ExchangeRateService;

#[AsCommand(
    name: 'app:fetch-rates',
    description: 'Fetch exchange rate from all datasources',
    aliases: ['app:get-rates'],
)]
class FetchRatesCommand extends Command
{
    public function __construct(
        private ExchangeRateService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('currencyCode', InputArgument::OPTIONAL, 'Currency code', BaseCurrencyEnum::getDefault()->value);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $currencyCode = strtoupper($input->getArgument('currencyCode'));

        $io->info(sprintf('You passed a currency code: %s', $currencyCode));

        $io->info('Process...');

        $result = $this->service->fetchAndSaveRates($currencyCode);

        foreach ($result as $key => $value) {
            if ($value) {
                $io->success(sprintf('%s rates have been successfully retrieved from %s.', $currencyCode, $key));
            } else {
                $io->warning(sprintf('Unfortunately, the %s rates was not loaded from %s.', $currencyCode, $key));
            }
        }

        return Command::SUCCESS;
    }
}
