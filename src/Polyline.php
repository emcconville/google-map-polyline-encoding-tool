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

class Polyline {
	
	/**
	 * @var array $polylines
	 */
	private $polylines = array();
	
	/**
	 * @var Polyline $instance
	 */
	private static $instance;
	
	/**
	 * Private constructor. Initialize class through Polyline::Singleton
	 *
	 * @see Polyline::Singleton
	 */
	private function __construct() {} 

	/**
	 * Static instance method
	 * 
	 * @return Polyline
	 */
	public static function Singleton() {
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
	 */
	public function __call($method,$arguments) {
		$return = null;
		if (preg_match('/^get(.+?)(points|encoded)$/i',$method,$matches)) {
			list($all,$node,$type) = $matches;
			return $this->getPolyline(strtolower($node),strtolower($type));
		} elseif (preg_match('/^get(points|encoded)$/i',$method,$matches)) {
			list($all,$type) = $matches;
			$node = array_shift($arguments);
			return $this->getPolyline(strtolower($node),strtolower($type));
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
	 */
	public function getPolyline($node, $type) {
		$node = strtolower($node);
		$type = in_array($type,array('points','encoded')) ? $type : 'encoded';
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
	 */
	public function polyline() {
		$arguments = func_get_args();
		$return = null;
		switch (count($arguments)) {
			case 2 :
				list($node,$value) = $arguments;
				$isArray = is_array($value);
				$return = $this->polylines[strtolower($node)] = array(
						'points'  => $isArray ? self::Flatten($value) : self::Decode($value),
						'encoded' => $isArray ? self::Encode($value) : $value
					);
				$return = $return[$isArray ? 'encoded' : 'points' ];
				break;
			case 1 :
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
	 * @return array $polylines
	 */
	public function listPolylines() {
		return $return = array_keys($this->polylines);
	}

	/**
	 * Apply Google Polyline algorithm to list of points
	 *
	 * @param array $points
	 * @param integer $precision optional
	 * @return string $encoded_string
	 */
  final public static function Encode($points, $precision = 5) {
		$points = self::Flatten($points);
		$encoded_string = '';
		$index = 0;
		$previous = array(0,0);
		foreach($points as $number) {
			$number = (float)($number);
			$number = floor($number * pow(10, $precision));
			$diff = $number - $previous[$index % 2];
			$previous[$index % 2] = $number;
			$number = $diff;
			$index++;
			$number = ($number < 0) ? ~($number << 1) : ($number << 1);
			$chunk = '';
			while($number >= 0x20) {
				$chunk .= chr((0x20 | ($number & 0x1f)) + 63);
				$number >>= 5;
			}
			$chunk .= chr($number + 63);
			$encoded_string .= $chunk;
		}
		return $encoded_string;
	}
	
	/**
	 * Reverse Google Polyline algorithm on encoded string
	 *
	 * @param string $string
	 * @param integer $precision optional
	 * @return array $points
	 */
  final public static function Decode($string, $precision = 5) {
		$points = array();
		$index = $i = 0;
		$previous = array(0,0);
		while( $i < strlen($string)  ) {
			$shift = $result = 0x00;
			do {
				$bit = ord(substr($string,$i++)) - 63;
				$result |= ($bit & 0x1f) << $shift;
				$shift += 5;
			} while( $bit >= 0x20 ) ;

			$diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
			$number = $previous[$index % 2] + $diff;
			$previous[$index % 2] = $number;
			$index++;
			$points[] = $number * 1 / pow(10, $precision);
		}
		return $points;
	}
	
	/**
	 * Reduce multi-dimensional to single list
	 *
	 * @param array $array
	 * @return array $flatten
	 */
	final public static function Flatten($array) {
		$flatten = array();
		foreach(array_values($array) as $node) {
			if (is_array($node)) {
				$flatten = array_merge($flatten,self::Flatten($node));
			} else {
				$flatten[] = $node;
			}
		}
		return $flatten;
	}
	
	/**
	 * Concat list into pairs of points
	 *
	 * @param array $list
	 * @return array $pairs
	 */
	final public static function Pair($list) {
		$pairs = array();
		if(!is_array($list)) { return $pairs; }
		do {
			$pairs[] = array(
					array_shift($list),
					array_shift($list)
				);
		} while (!empty($list));
		return $pairs;
	}
}
