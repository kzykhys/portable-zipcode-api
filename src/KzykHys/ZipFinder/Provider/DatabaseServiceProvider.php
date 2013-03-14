<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Provider;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\ServiceProviderInterface;

/**
 * Provides Doctrine DBAL (Silex) and Doctrine ORM
 *
 * Services:
 *     - orm.em              Doctrine ORM EntityManager
 *     - orm.schema_tool     Doctrine ORM SchemaTool
 *     - repository.address  Doctrine ORM EntityRepository
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class DatabaseServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => $app['path.sqlite.db'],
            ),
        ));

        /**
         * @return string
         */
        $app['orm.entity.path'] = $app->share(function () {
            return [
                dirname((new \ReflectionClass('\\KzykHys\\ZipFinder\\Entity\\Address'))->getFileName())
            ];
        });

        /**
         * @param  Application $app
         *
         * @return \Doctrine\Common\Cache\Cache
         */
        $app['orm.cache.driver'] = function (Application $app) {
            if (extension_loaded('apc')) {
                return new ApcCache();
            } else {
                if (defined('PHAR_RUNNING')) {
                    return new ArrayCache();
                } else {
                    return new FilesystemCache($app['path.cache'] . $app['orm.cache.path']);
                }
            }
        };

        /**
         * @param  Application $app
         *
         * @return \Doctrine\ORM\Configuration
         */
        $app['orm.config'] = $app->share(function (Application $app) {
            $config = new Configuration();
            $config->setMetadataCacheImpl($app['orm.cache.driver']);
            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($app['orm.entity.path']));
            $config->setQueryCacheImpl($app['orm.cache.driver']);
            $config->setProxyDir($app['path.cache'] . $app['orm.proxy.path']);
            $config->setProxyNamespace($app['orm.proxy.namespace']);
            $config->setAutoGenerateProxyClasses($app['debug']);

            return $config;
        });

        /**
         * @param  Application $app
         *
         * @return \Doctrine\ORM\EntityManager
         */
        $app['orm.em'] = $app->share(function (Application $app) {
            return EntityManager::create($app['db'], $app['orm.config'], $app['db.event_manager']);
        });

        /**
         * @param  Application $app
         *
         * @return \Doctrine\ORM\Tools\SchemaTool
         */
        $app['orm.schema_tool'] = $app->share(function (Application $app) {
            return new SchemaTool($app['orm.em']);
        });

        /**
         * Returns EntityRepository
         */
        $app['repository.address'] = $app->share(function (Application $app) {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $app['orm.em'];

            /** @var \Doctrine\ORM\EntityRepository $repository */
            $repository = $em->getRepository('KzykHys\\ZipFinder\\Entity\\Address');

            return $repository;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

}