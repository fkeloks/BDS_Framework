<?php

namespace BDSConsole;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServer extends Command
{

    protected function configure() {
        $this->setName('server:start');
        $this->setDescription('Starting the web server');
        $this->setHelp('This command starts the web server');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln(' ');
        try {
            exec('php bin/composer.phar dump-autoload');
        } catch (Exception $e) {
            var_dump($e);
        }
        $output->writeln([
            '<fg=black;bg=green>',
            '                                                                                                         ',
            '    [BDS Framework : INFO] Development Server started on http://localhost:8000                           ',
            '    [Press Ctrl-C to quit.]                                                                              ',
            '                                                                                                         ',
            '</>'
        ]);
        try {
            exec('php -S localhost:8000');
        } catch (Exception $e) {
            var_dump($e);
        }
    }

}
