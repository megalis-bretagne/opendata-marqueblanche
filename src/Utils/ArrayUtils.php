<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Utils;

/**
 * Utils class to manipulate array.
 *
 * @package DataSearchEngine\Utils;
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class ArrayUtils {

    /**
	 * Get an item from an array.
	 *
     * @param  array $array Array in which item must be retrieved
     * @param  string $key Item key in this array
     * @param  mixed $default (optiona) Default value to return if key is not found
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return mixed
     * @access public
	 */
    public static function get($array, $key, $default = null) {
        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') !== false) {
            $array = static::findDot($array, $key);
            if (static::exists($array, $key)) {
                return $array[$key];
            }
        }

        return $default;
    }

    /**
	 * Check if a key exists in an array.
	 *
     * @param  array $array Array in which item must be retrieved
     * @param  string $key Item key in this array
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return boolean
     * @access public
	 */
    public static function exists($array, $key) {
        return array_key_exists($key, $array);
    }

    /**
	 * Find an array value in an array based on a relational key.
	 *
     * @param  array $array Array in which item must be retrieved
     * @param  string $key Item key in this array
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return array
     * @access public
	 */
    public static function findDot($array, $key) {
        $result = static::findFlatKey('.', $array, $key);

        return $result ? [$result['key'] => $result['value']] : [];
    }

    /**
	 * Find the nested value of an array using the given separator-notation key.
	 *
     * @param  string $separator Char separator
     * @param  array $array Array in which item must be retrieved
     * @param  string $key Item key in this array
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return array|null
     * @access public
	 */
    public static function findFlatKey($separator, $array, $key) {
        $keysPath = [];
        $result = null;
        if (strpos($key, $separator) !== false) {
            $keys = explode($separator, $key);
            $value = $array;

            while ($keys) {
                $k = array_shift($keys);

                if (!array_key_exists($k, $value)) {
                    break;
                }

                $value = $value[$k];
                $keysPath[] = $k;

                if ($key == implode($separator, $keysPath)) {
                    $result = [
                        'key' => $key,
                        'value' => $value
                    ];
                }

                // Stop the search if the next value is not an array
                if (!is_array($value)) {
                    break;
                }
            }
        }

        return $result;
    }
}