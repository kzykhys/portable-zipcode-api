<?php
/**
 * Bootstrap script for console
 *
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 * @author    Kazuyuki Hayashi <hayashi@valnur.net>
 */

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

$app = require __DIR__ . '/bootstrap.php';

$app->boot();

$console = new \Symfony\Component\Console\Application('kzykhys/zip-finder', '1.0.0');
$console->getHelperSet()->set(new ConnectionHelper($app['db']), 'db');
$console->getHelperSet()->set(new EntityManagerHelper($app['orm.em']), 'em');

$createCommand = new CreateCommand();
$updateCommand = new UpdateCommand();
$dropCommand   = new DropCommand();

$console->addCommands(
    array(
        $createCommand->setName('doctrine:schema:create'),
        $updateCommand->setName('doctrine:schema:update'),
        $dropCommand->setName('doctrine:schema:drop'),
        new \KzykHys\ZipFinder\Command\CSV\ImportCommand($app),
        new \KzykHys\ZipFinder\Command\Build\PharCommand(),
        new \KzykHys\ZipFinder\Command\CSV\DownloadCommand(),
        new \KzykHys\ZipFinder\Command\Build\PackageCommand()
    )
);

$console->run();