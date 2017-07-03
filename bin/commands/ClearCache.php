<?php

namespace BDSConsole;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache extends Command
{

    protected function configure() {
        $this->setName('cache:clear');
        $this->setDescription('Removes the framework cache');
        $this->setHelp('This command removes the framework cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        function clear_dir($directory, $delete = false) {
            $dir = opendir($directory);
            while ($file = readdir($dir)) {
                if (!in_array($file, ['.', '..'])) {
                    if (is_dir("$directory/$file")) {
                        clear_dir("$directory/$file", true);
                    } else {
                        unlink("$directory/$file");
                    }
                }
            }
            closedir($dir);
            if($delete == true) {
                try {
                    rmdir("$directory/$file");
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            }
            return true;
        }

        $file = clear_dir(\BDSCore\Config::getConfig('twigCache'));

        if(!$file) {
            $output->writeln([
                ' ',
                '<fg=white;bg=red>',
                '                                                                                                         ',
                '    [BDS Framework : ERROR]                                                                              ',
                htmlspecialchars($file),
                '                                                                                                         ',
                '</>'
            ]);
        }

        $output->writeln([
            ' ',
            '    [BDS Framework : INFO] Clearing the cache...',
            '<fg=black;bg=green>',
            '                                                                                                         ',
            '    [BDS Framework : OK] Cache was successfully cleared.                                                 ',
            '                                                                                                         ',
            '</>'
        ]);
    }

}
