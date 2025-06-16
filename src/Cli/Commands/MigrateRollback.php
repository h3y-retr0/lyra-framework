<?php

namespace Lyra\Cli\Commands;

use Lyra\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'migrate:rollback', description: 'Rollback migrations')]
class MigrateRollback extends Command {
    protected function configure() {
        $this->addArgument("steps", InputArgument::OPTIONAL, "Amount of migrations to reverse, all by default");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            app(Migrator::class)->rollback($input->getArgument('steps') ?? null);
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Could not reverse migrations: {$e->getMessage()}</error>");
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
