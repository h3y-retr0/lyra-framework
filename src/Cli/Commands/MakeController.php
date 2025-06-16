<?php

namespace Lyra\Cli\Commands;

use Lyra\App;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:controller', description: 'Create a new controller')]
class MakeController extends Command {
    protected function configure() {
        $this->addArgument("name", InputArgument::REQUIRED, "Controller name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument("name");
        $template = file_get_contents(resourcesDirectory() . "/templates/controller.php");
        $template = str_replace("ControllerName", $name, $template);
        file_put_contents(App::$root . "/app/Controllers/$name.php", $template);
        $output->writeln("<info>Controller created => $name.php</info>");

        return Command::SUCCESS;
    }
}
