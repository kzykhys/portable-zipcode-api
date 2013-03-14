<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Provider;

use KzykHys\ZipFinder\Bridge\Symfony\Encoder\PhpEncoder;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Provides symfony/serializer
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class SerializerServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['serializer.encoders'] = $app->share(function () {
            return array(new XmlEncoder(), new JsonEncoder(), new PhpEncoder());
        });

        $app['serializer'] = $app->share(function (Application $app) {
            return new Serializer(array(new GetSetMethodNormalizer()), $app['serializer.encoders']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

}