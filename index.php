<?php
/**
 * Web frontend
 *
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 * @author    Kazuyuki Hayashi <hayashi@valnur.net>
 */

use Doctrine\ORM\NoResultException;
use KzykHys\ZipFinder\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/** @var $app KzykHys\ZipFinder\Application */
$app = require __DIR__ . '/app/bootstrap.php';

$app['zip.controller'] = $app->share(function () {
    return new \KzykHys\ZipFinder\Controller();
});

$app['controllers']
    ->assert('code', '\d{3}-?\d{4}')
    ->assert('format', '(json|xml|php)')
    ->value('format', 'json');

$app->get('/version', 'zip.controller:versionAction');
$app->get('/api', 'zip.controller:apiAction');
$app->get('/search/{code}', 'zip.controller:getSearchAction');
$app->get('/search/{code}.{format}', 'zip.controller:getSearchAction')->bind('search_code');
$app->post('/search', 'zip.controller:postSearchAction')->bind('search');

$app->error(function (\InvalidArgumentException $e, $code) use ($app) {
    return $app->json(array(
        'result'  => false,
        'reason'  => 400,
        'message' => 'Bad Request'
    ), 400);
});

$app->error(function (NoResultException $e, $code) use ($app) {
    return $app->json(array(
        'result'  => false,
        'reason'  => 404,
        'message' => 'No Results Found'
    ), 404);
});

$app->error(function (HttpException $e, $code) use ($app) {
    return $app->json(array(
        'result'  => false,
        'reason'  => $e->getStatusCode(),
        'message' => $e->getMessage()
    ), $e->getStatusCode());
});

$app->error(function (\Exception $e, $code) use ($app) {
    return $app->json(array(
        'result'  => false,
        'reason'  => 500,
        'message' => 'Error:' . $code
    ), 500);
});

$app->run();