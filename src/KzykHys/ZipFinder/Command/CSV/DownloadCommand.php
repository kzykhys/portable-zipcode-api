<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Command\CSV;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides `csv:download` command
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class DownloadCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('csv:download')
            ->setDescription('Download Lzh archive from JP Post website')
            ->addArgument('path', InputArgument::OPTIONAL, 'The directory to place lzh files', './csv');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://www.post.japanpost.jp/zipcode/dl/kogaki/lzh/';
        $files = array(
            '01hokkai.lzh',
            '02aomori.lzh',
            '03iwate.lzh',
            '04miyagi.lzh',
            '05akita.lzh',
            '06yamaga.lzh',
            '07fukush.lzh',
            '08ibarak.lzh',
            '09tochig.lzh',
            '10gumma.lzh',
            '11saitam.lzh',
            '12chiba.lzh',
            '13tokyo.lzh',
            '14kanaga.lzh',
            '15niigat.lzh',
            '16toyama.lzh',
            '17ishika.lzh',
            '18fukui.lzh',
            '19yamana.lzh',
            '20nagano.lzh',
            '21gifu.lzh',
            '22shizuo.lzh',
            '23aichi.lzh',
            '24mie.lzh',
            '25shiga.lzh',
            '26kyouto.lzh',
            '27osaka.lzh',
            '28hyogo.lzh',
            '29nara.lzh',
            '30wakaya.lzh',
            '31tottor.lzh',
            '32shiman.lzh',
            '33okayam.lzh',
            '34hirosh.lzh',
            '35yamagu.lzh',
            '36tokush.lzh',
            '37kagawa.lzh',
            '38ehime.lzh',
            '39kochi.lzh',
            '40fukuok.lzh',
            '41saga.lzh',
            '42nagasa.lzh',
            '43kumamo.lzh',
            '44oita.lzh',
            '45miyaza.lzh',
            '46kagosh.lzh',
            '47okinaw.lzh',
        );

        $target = $input->getArgument('path');

        $output->writeln('Downloading');

        /** @var ProgressHelper $progress */
        $progress = $this->getHelperSet()->get('progress');
        $progress->setFormat(ProgressHelper::FORMAT_NORMAL);
        $progress->start($output, count($files));

        foreach ($files as $file) {
            $path = $url . $file;
            file_put_contents($target.'/'.$file, file_get_contents($path));
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('Csv file is under ' . $target);
    }

}