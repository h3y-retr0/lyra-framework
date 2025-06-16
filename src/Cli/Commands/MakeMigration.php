<?php

namespace Lyra\Cli\Commands;

use Lyra\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:migration', description: 'Create new migration')]
class MakeMigration extends Command {
    protected function configure() {
        $this->addArgument("name", InputArgument::REQUIRED, "Migration name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        app(Migrator::class)->make($input->getArgument('name'));
        return Command::SUCCESS;
    }
}
