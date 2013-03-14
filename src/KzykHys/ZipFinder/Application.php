<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder;

use Doctrine\ORM\NoResultException;
use Silex\Application\UrlGeneratorTrait;
use Symfony\Component\Serializer\Serializer;

/**
 * Portable ZipCode API
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class Application extends \Silex\Application
{

    use UrlGeneratorTrait;

    /**
     * @param $code
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function find($code)
    {
        $code = str_replace('-', '', $code);

        if (!is_numeric($code) || strlen($code) != 7) {
            throw new \InvalidArgumentException('Zip code must be /^\\d{3}-?\\d{4}$/', 1);
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this['repository.address'];

        $data = $repository->findOneBy(array('code' => $code));

        if (!$data) {
            throw new NoResultException('The address is not found', -1);
        }

        return $data;
    }

    /**
     * @param $data
     * @param $format
     *
     * @return string
     */
    public function serialize($data, $format)
    {
        /** @var Serializer $serializer  */
        $serializer = $this['serializer'];

        return $serializer->serialize($data, $format);
    }

}