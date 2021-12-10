<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Utils;

use DateTime;

/**
 * NormalizeString utils class to sanitize string.
 *
 * @package DataSearchEngine\Utils
 * @author 	   Xavier MADIOT <x.madiot@girondenumerique.fr>
 */ 
class NormalizeString {

    /**
     * Normalize string with replacing specials characters with an optional char.
     * If it's a filename with extension, the extension is saved.
	 * 
     *
     * @param string $text String to normalize
	 * @param string $space Optional replacement character ("_" by default)
     * @param boolean $unify Optional flag to unify string with current date
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     */
    public static function normalize(string $text, $space = '_', $unify = true) {
        if ($unify) {
            $extension = pathinfo($text, PATHINFO_EXTENSION);   // Save extension
            $text = rtrim($text, $extension);                   // Remove extension if it's a file
        }
        $text = self::removeSpecialsChars($text, $space);       // Remove specials characters
        $text = preg_replace('/\W+/', '', $text);               // Replace others specials chars
        $text = strtolower($text);                              // In minus
        $text = trim($text);                                    // Delete space before and end
        $text = substr($text, 0, 50);                           // Limit size to 50 !
        $text = ltrim($text, $space);                           // Delete replacement char on left
        $text = rtrim($text, $space);                           // Delete replacement char on right

        if ($unify) {
            $text = $text.$space.self::getMicroTime();

            // If it's a file with an extension
            if (!empty($extension)) {
                $text = $text.'.'.$extension;
            }
        }
        
        return $text;
    }

    /**
     * Get micro time date to unify string.
	 * 
     *
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     */
    private static function getMicroTime() {
        $t = microtime(true);
        $micro = sprintf('%6d', ($t - floor($t)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.'.trim($micro), $t));

        return $date->format('YmdHisu');
    }
 
    /**
     * Replace accented characters and others specials cases.
	 * 
     *
     * @param string $text String to normalize
	 * @param string $space Optional replacement character
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     */
    private static function removeSpecialsChars(string $text, $space) {
        $utf8 = array(
            '/[áàâãªäåæ]/u' => 'a',
            '/[ÁÀÂÃÄÅÆ]/u' => 'A',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[ÍÌÎÏÍ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[óòôõºöðø]/u' => 'o',
            '/[ÓÒÔÕÖØ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/[ýýÿ]/u' => 'y',
            '/Š/u' => 'S',
            '/š/u' => 's',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/Ð/' => 'Dj',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/Ý/' => 'Y',
            '/Ž/' => 'Z',
            '/ž/' => 'z',
            '/þ/' => 'b',
            '/Þ/' => 'B',
            '/ƒ/' => 'f',
            '/ß/' => 'ss',
            '/Œ/' => 'Oe',
            '/œ/' => 'oe',
            '/–/' => $space,
            '/-/' => $space,
            '/[‘’‚‹›]/u' => '',
            '/[“”«»„]/u' => '',
            '/ /' => $space,
            '/ /' => $space
        );
        $text = preg_replace(array_keys($utf8), array_values($utf8), $text);
 
        return $text;
    }
}