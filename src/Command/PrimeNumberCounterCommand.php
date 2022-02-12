<?php

declare(strict_types=1);

namespace Thekeymaster\CodingChallenge\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Thekeymaster\CodingChallenge\MathJsClient;

class PrimeNumberCounterCommand extends Command
{
    private const MAX_NUMBER = 5000000;

    protected static $defaultName = 'count:prime-numbers';

    protected function configure(): void
    {
        $this
            ->setHelp('Returns the number of prime numbers that are strictly less than n')
            ->addArgument(
                'maxNumber',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $argument = $input->getArgument('maxNumber');
        if (!is_numeric($argument)) {
            $io->error('Given argument must be numeric!');
            return Command::FAILURE;
        }

        $number = (int)$argument;
        if ($number > self::MAX_NUMBER) {
            $io->error(sprintf('Given argument must not be higher than %d!', self::MAX_NUMBER));
            return Command::FAILURE;
        }

        $client = new MathJsClient();
        $rawResponse = $client->sendExpression($this->buildPrimeNumberExpression($number));

        $response = json_decode($rawResponse, true);
        $resultCounts = array_count_values($response['result']);

        $io->success(sprintf(
            'There are %d prime numbers less than %d',
            $resultCounts['true'],
            $number
        ));

        return Command::SUCCESS;
    }

    private function buildPrimeNumberExpression(int $number): array
    {
        // Explicitly reduce number by 1, as the prime number count should be strictly below the given number.
        return array_map(fn(int $n) => sprintf('isPrime(%d)', $n), range(0, $number - 1));
    }
}
