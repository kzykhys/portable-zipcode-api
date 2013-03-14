<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Command\CSV;

use Doctrine\ORM\EntityManager;
use KzykHys\CsvParser\CsvParser;
use KzykHys\ZipFinder\Application;
use KzykHys\ZipFinder\Entity\Address;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Provides `csv:import` command
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class ImportCommand extends Command
{

    /**
     * @var \KzykHys\ZipFinder\Application
     */
    private $app;

    /**
     * @param \KzykHys\ZipFinder\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Import addresses from CSV')
            ->addArgument('path', InputArgument::REQUIRED, 'The path to search')
            ->addOption('--append', '-a', InputOption::VALUE_OPTIONAL, 'Load records without purging database', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        /** @var EntityManager $em */
        $em = $this->app['orm.em'];

        if (!$input->getOption('append')) {
            $output->writeln('Purging database');
            $em->getConnection()->executeUpdate("DELETE FROM address");
        }

        if (is_dir($path)) {
            $finder = new Finder();
            $finder->in($path)->name('/\.csv/i');

            $output->writeln('Loading CSV from ' . $path . ' (' . $finder->count() . ' files)');

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $this->loadFile($output, $file->getPathname());
            }
        } else {
            $this->loadFile($output, $path);
        }

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param string          $path
     */
    protected function loadFile(OutputInterface $output, $path)
    {
        /** @var EntityManager $em */
        $em   = $this->app['orm.em'];

        $output->write('Loading records from ' . $path . ' ... ');

        $parser = CsvParser::fromFile($path);
        $data = $parser->parse();

        foreach ($data as $value) {
            $address = new Address();
            $address->code = $value[2];
            $address->pref = $value[6];
            $address->city = $value[7];
            $address->town = $value[8];

            $em->persist($address);
        }

        $parser = null;

        $em->flush();

        $output->writeln('Done');
    }

}