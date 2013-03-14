<?php
/**
 * Bootstrap script
 *
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 * @author    Kazuyuki Hayashi <hayashi@valnur.net>
 */

use KzykHys\ZipFinder\Application;

$loader = require __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app['version']             = '1.0.0';
$app['locale']              = 'ja';
$app['debug']               = true;
$app['path.app']            = defined("PHAR_RUNNING") ? 'phar://zip.phar/app' : __DIR__;
$app['path.cache']          = $app['path.app'] . '/cache';
$app['path.api.js']         = $app['path.app'] . '/api.js';
$app['path.sqlite.db']      = (defined("PHAR_RUNNING") ? dirname(Phar::running(false)) : $app['path.app']) . '/zip.sqlite.db';
$app['orm.cache.path']      = '/doctrine/orm';
$app['orm.proxy.path']      = '/doctrine/proxies';
$app['orm.proxy.namespace'] = 'KzykHys\\ZipFinder\\Proxy\\ORM';
$app['api.js']              = "(function($){\$.zipSearch=function(a){return $.ajax({url:'%URL%/'+a,type:'get'})}})(jQuery);";

/*
(function ($) {
    $.zipSearch = function (zipcode) {
        return $.ajax({
            url:  '%URL%' + zipcode,
            type: 'get'
        });
    };
})(jQuery);
*/

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \KzykHys\ZipFinder\Provider\DatabaseServiceProvider());
$app->register(new \KzykHys\ZipFinder\Provider\SerializerServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

return $app;