<?php

/**
 * Polyline
 *
 * A simple class to handle polyline-encoding for Google Maps
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   Polyline
 * @version   @VERSION@
 * @copyright @DATE@ emcconville
 * @license   GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @link      https://github.com/emcconville/google-map-polyline-encoding-tool
 * @author    E. McConville <emcconville@emcconville.com>
 */

//@NAMESPACE@ namespace emcconville {

class Polyline
{
    /**
     * @var array $polylines
     * @deprecated
     * @ignore
     */
    private $polylines = array();

    /**
     * Default precision level of 1e-5.
     *
     * Overwrite this property in extended class to adjust precision of numbers.
     * !!!CAUTION!!!
     * 1) Adjusting this value will not guarantee that third party
     *    libraries will understand the change.
     * 2) Float point arithmetic IS NOT real number arithmetic. PHP's internal
     *    float precision may contribute to undesired rounding.
     *
     * @var int $precision
     */
    protected static $precision = 5;

    /**
     * @var Polyline $instance
     * @deprecated
     * @ignore
     */
    private static $instance;

    public function __construct()
    {
      // Overloading bug #11
    }

    /**
     * Static instance method
     *
     * @return Polyline
     * @deprecated
     * @codeCoverageIgnore
     * @ignore
     */
    public static function Singleton()
    {
        trigger_error('Polyline::Singleton deprecated.', E_USER_DEPRECATED);
        return self::$instance instanceof self ? self::$instance : self::$instance = new self;
    }

    /**
     * Magic method for supporting wildcard getters
     *
     * @let {Node} be the name of the polyline
     * @method get{Node}Points(   ) //=> array of points for polyline "Node"
     * @method get{Node}Encoded(  ) //=> encoded string  for polyline "Node"
     * @method getPoints( "{Node}") //=> array of points for polyline "Node"
     * @method getEncoded("{Node}") //=> encoded string  for polyline "Node"
     * @deprecated
     * @codeCoverageIgnore
     * @ignore
     */
    public function __call($method,$arguments)
    {
        trigger_error('Polyline::__call('.$method.') deprecated.', E_USER_DEPRECATED);
        $return = null;
        if (preg_match('/^get(.+?)(points|encoded)$/i', $method, $matches)) {
            list($all,$node,$type) = $matches;
            return $this->getPolyline(strtolower($node), strtolower($type));
        } elseif (preg_match('/^get(points|encoded)$/i', $method, $matches)) {
            list($all,$type) = $matches;
            $node = array_shift($arguments);
            return $this->getPolyline(strtolower($node), strtolower($type));
        } else {
            throw new BadMethodCallException();
        }
        return $return;
    }

    /**
     * Polyline getter
     * @param string $node
     * @param string $type
     * @return mixed
     * @deprecated
     * @codeCoverageIgnore
     * @ignore
     */
    public function getPolyline($node, $type)
    {
        trigger_error('Polyline::getPolyline deprecated.', E_USER_DEPRECATED);
        $node = strtolower($node);
        $type = in_array($type, array('points','encoded')) ? $type : 'encoded';
        return isset($this->polylines[$node])
                    ? $this->polylines[$node][$type]
                    : ($type =='points' ? array() : null);
    }

    /**
     * General purpose data method
     *
     * @param string polyline name
     * @param mixed [ string | array ] optional
     * @return array
     * @deprecated
     * @codeCoverageIgnore
     * @ignore
     */
    public function polyline()
    {
        trigger_error('Polyline::polyline deprecated.', E_USER_DEPRECATED);
        $arguments = func_get_args();
        $return = null;
        switch (count($arguments)) {
            case 2:
                list($node,$value) = $arguments;
                $isArray = is_array($value);
                $return = $this->polylines[strtolower($node)] = array(
                        'points'  => $isArray ? self::Flatten($value) : self::Decode($value),
                        'encoded' => $isArray ? self::Encode($value) : $value
                    );
                $return = $return[$isArray ? 'encoded' : 'points' ];
                break;
            case 1:
                $node = strtolower((string)array_shift($arguments));
                $return = isset($this->polylines[$node])
                        ? $this->polylines[$node]
                        : array( 'points' => null, 'encoded' => null );
                break;
        }
        return $return;
    }

    /**
     * Retrieve list of polyline within singleton
     *
     * @return array polylines
     * @deprecated
     * @codeCoverageIgnore
     * @ignore
     */
    public function listPolylines()
    {
        trigger_error('Polyline::listPolylines deprecated.', E_USER_DEPRECATED);
        return $return = array_keys($this->polylines);
    }

    /**
     * Apply Google Polyline algorithm to list of points
     *
     * @param array $points
     * @param integer $precision optional
     * @return string encoded string
     */
    final public static function Encode($points)
    {
        $points = self::Flatten($points);
        $encodedString = '';
        $index = 0;
        $previous = array(0,0);
        foreach ($points as $number) {
            $number = (float)($number);
            $number = (int)round($number * pow(10, static::$precision));
            $diff = $number - $previous[$index % 2];
            $previous[$index % 2] = $number;
            $number = $diff;
            $index++;
            $number = ($number < 0) ? ~($number << 1) : ($number << 1);
            $chunk = '';
            while ($number >= 0x20) {
                $chunk .= chr((0x20 | ($number & 0x1f)) + 63);
                $number >>= 5;
            }
            $chunk .= chr($number + 63);
            $encodedString .= $chunk;
        }
        return $encodedString;
    }

    /**
     * Reverse Google Polyline algorithm on encoded string
     *
     * @param string $string
     * @param integer $precision optional
     * @return array points
     */
    final public static function Decode($string)
    {
        $points = array();
        $index = $i = 0;
        $previous = array(0,0);
        while ($i < strlen($string)) {
            $shift = $result = 0x00;
            do {
                $bit = ord(substr($string, $i++)) - 63;
                $result |= ($bit & 0x1f) << $shift;
                $shift += 5;
            } while ($bit >= 0x20);

            $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $number = $previous[$index % 2] + $diff;
            $previous[$index % 2] = $number;
            $index++;
            $points[] = $number * 1 / pow(10, static::$precision);
        }
        return $points;
    }

    /**
     * Reduce multi-dimensional to single list
     *
     * @param array $array
     * @return array flattened
     */
    final public static function Flatten($array)
    {
        $flatten = array();
        array_walk_recursive(
            $array, // @codeCoverageIgnore
            function ($current) use (&$flatten) {
                $flatten[] = $current;
            }
        );
        return $flatten;
    }

    /**
     * Concat list into pairs of points
     *
     * @param array $list
     * @return array pairs
     */
    final public static function Pair($list)
    {
        $pairs = array();
        if (!is_array($list)) {
            return $pairs;
        }
        do {
            $pairs[] = array(
                    array_shift($list),
                    array_shift($list)
                );
        } while (!empty($list));
        return $pairs;
    }
}

//@NAMESPACE@ }
