<?php

namespace Lyra\Cli\Commands;

use Lyra\App;
use Lyra\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:model', description: 'Create a new model')]
class MakeModel extends Command {
    protected function configure() {
        $this
            ->addArgument("name", InputArgument::REQUIRED, "Model name")
            ->addOption("migration", "m", InputOption::VALUE_OPTIONAL, "Also create a migration file", false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument("name");
        $migration = $input->getOption("migration");

        $template = file_get_contents(resourcesDirectory() . "/templates/model.php");
        $template = str_replace("ModelName", $name, $template);
        file_put_contents(App::$root . "/app/Models/$name.php", $template);
        $output->writeln("<info>Model created => $name.php</info>");

        if ($migration !== false) {
            app(Migrator::class)->make("create_{$name}s_table");
        }

        return Command::SUCCESS;
    }
}
