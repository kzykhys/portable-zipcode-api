<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Bridge\Symfony\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * Serialize objects to PHP serial
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class PhpEncoder implements EncoderInterface
{

    const FORMAT = 'php';

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = array())
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return self::FORMAT === $format;
    }

}