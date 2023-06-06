<?php

/*
 * This file is part of the Chellal project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Hello MONARKIT
 *
 */

namespace App\Twig;

use App\Entity\Media;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppFileExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_file_resolve_url', [$this, 'resolveUrl']),
        ];
    }

    /**
     * Returns the public url of a given file.
     *
     * @return string
     */
    public function resolveUrl(Media $file): string
    {
        return '/uploads/'.$file->getRealName();
    }

}
