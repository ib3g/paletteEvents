<?php

/*
 * This file is part of the Chellal  project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Monarkit
 *
 */

namespace App\Util;

use Cocur\Slugify\Slugify;

class StringUtil
{

    /**
     * Generate a Random string of $length characters.
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

}
