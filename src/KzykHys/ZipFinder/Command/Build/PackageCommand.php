<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Command\Build;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides `build:package` command
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class PackageCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build:package')
            ->setDescription('Build distribution package');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = 'build/portable-zip-api.zip';

        $pharCommand = $this->getApplication()->find('build:phar');

        $arrayInput = new ArrayInput(array(
            'command' => 'build:phar'
        ));
        $arrayInput->setInteractive(false);

        if ($pharCommand->run($arrayInput, $output)) {
            $output->writeln('The operation is aborted due to build:phar command');

            return 1;
        }

        if (file_exists($file)) {
            $output->writeln('Removing previous package');
            unlink($file);
        }

        $zip = new \ZipArchive();
        if ($zip->open($file, \ZipArchive::CREATE) !== true) {
            $output->writeln('Failed to open zip archive');

            return 1;
        }

        $zip->addFile('build/zip.phar.php', 'zip.phar.php');
        $zip->addFile('app/zip.sqlite.db', 'zip.sqlite.db');
        $zip->addFile('README.md', 'README.md');
        $zip->close();

        return 0;
    }

}