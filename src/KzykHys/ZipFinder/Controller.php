<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder;

use KzykHys\ZipFinder\Bridge\Symfony\File\ExtensionMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles Web Request
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class Controller
{

    /**
     * Returns version
     *
     * @param Application $app
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function versionAction(Application $app)
    {
        return $app->json(array('version' => $app['version']));
    }

    /**
     * Returns JavaScript API
     *
     * @param Application $app
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function apiAction(Application $app)
    {
        $path = $app->path('search', array('code' => null, 'format' => null));
        $file = str_replace('%URL%', $path, $app['api.js']);

        return new Response($file, 200, array('Content-Type' => 'text/javascript'));
    }

    /**
     * Search address by POST request
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postSearchAction(Application $app, Request $request)
    {
        $data = $app->find($request->get('code'));

        return $this->createResponse($app, $data, $request->get('format', 'json'));
    }

    /**
     * Search address by GET request
     *
     * @param Application $app
     * @param             $code
     * @param string      $format
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSearchAction(Application $app, $code, $format = 'json')
    {
        $data = $app->find($code);

        return $this->createResponse($app, $data, $format);
    }

    /**
     * Serialize data and create response
     *
     * @param Application $app
     * @param mixed       $data
     * @param string      $format
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponse(Application $app, $data, $format = 'json')
    {
        $content = $app->serialize(array('result' => true, 'data' => $data), $format);

        $guesser = new ExtensionMimeTypeGuesser();
        $type = $guesser->guess('.'.$format);

        if (!$type) {
            $type = 'text/plain';
        }

        return new Response($content, 200, array('Content-Type' => $type, 'Cache-Control' => 's-maxage=3600, public'));
    }

}