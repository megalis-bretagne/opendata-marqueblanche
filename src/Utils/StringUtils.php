<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Utils;

/**
 * Utils class to manipulate string.
 *
 * @package DataSearchEngine\Utils
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class StringUtils {

    /**
	 * Format a string date (format d-m-Y) to a DateTime object.
	 *
	 * @param array $date String date
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return DateTime
	 * @access public
	 */
    public static function formatDateTime(string $date) {
		if (!is_string($date)) {
			throw new \Exception('La date passée en paramétre n\'est pas une chaîne de caractères.', 500);
		}
		
		$datetime = \DateTime::createFromFormat('d-m-Y', $date);
		if ($datetime === false) {
				throw new \Exception('La date "'.$date.'" n\'est pas au bon format (jj-mm-aaaa).', 500);
		} else {
				return $datetime;
		}
	}
}
