<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Command\Build;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Provides `build:phar` command
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class PharCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build:phar')
            ->setDescription('Build this application as a Phar package');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Building Phar archive');

        $file = 'build/zip.phar.php';

        if (file_exists($file)) {
            $output->writeln('Removing previous phar archive');
            unlink($file);
        }

        $output->writeln('Buffering contents ...');

        $phar = new \Phar($file, 0, 'zip.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = new Finder();
        $finder->in(array('app', 'src', 'vendor'))->files(array('*.php', '*.js'));
        $count = $finder->count();

        $output->writeln(sprintf('Found %d entries', $count));

        $step  = $count / 100;
        $index = 0;
        $indexStep = 0;

        /** @var ProgressHelper $progress */
        $progress = $this->getHelperSet()->get('progress');
        $progress->setFormat(' [%bar%] %percent%%');
        $progress->start($output, 100);

        /** @var SplFileInfo $file */
        $basePath = realpath(__DIR__ . '/../../../../../');
        foreach ($finder as $file) {
            $path = str_replace($basePath, '', $file->getRealPath());
            $path = ltrim($path, DIRECTORY_SEPARATOR);
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
            $phar->addFromString($path, $file->getContents());

            if ($index++ % $step == 0 && $indexStep < 100) {
                $progress->advance();
                $indexStep++;
            }
        }

        $phar->addFromString('index.php', file_get_contents($basePath . DIRECTORY_SEPARATOR . 'index.php'));

        $phar->setStub(
            '<?php Phar::mapPhar("zip.phar"); define("PHAR_RUNNING", true); /*Phar::interceptFileFuncs();*/ require "phar://zip.phar/index.php"; __HALT_COMPILER();'
        );
        $phar->stopBuffering();

        $progress->finish();

        $output->writeln('Build complete. Phar archive has been deployed to build/zip.phar.php');
    }

}