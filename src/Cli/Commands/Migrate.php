<?php

namespace Lyra\Cli\Commands;

use Lyra\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'migrate', description: 'Run migrations')]
class Migrate extends Command {
    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            app(Migrator::class)->migrate();
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Could not run migrations: {$e->getMessage()}</error>");
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
