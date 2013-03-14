<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Bridge\Symfony\File;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

/**
 * Guesses MIME type from extension
 *
 * @author Kazuyuki Hayashi <hayashi@valur.net>
 */
class ExtensionMimeTypeGuesser extends MimeTypeExtensionGuesser
{

    /**
     * @param string $path
     *
     * @return bool|string
     */
    public function guess($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return array_search($ext, $this->defaultExtensions);
    }

}