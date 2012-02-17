<?php

class Polyline {
	private $polylines = array();
	private static $instance;
	private function __constuct() {
		
	} 

	public static function Singleton() {
		return self::$instance instanceof self ? self::$instance : self::$instance = new self;
	}

	public function __call($method,$agruments) {
		$return = null;
		if (preg_match('/^get(.+?)(points|encoded)$/i',$method,$matches)) {
			list($all,$key,$type) = $matches;
			if(isset($this->polylines[strtolower($key)])) {
				$return = $this->polylines[strtolower($key)][strtolower($type)];
			} else {
				throw new BadMethodCallException();
			}
		} else {
			throw new BadMethodCallException();
		}
		return $return;
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
				$return = $this->polylines[$node] = array(
						'points'  => is_array($value) ? self::flatten($value) : self::Decode($value),
						'encoded' => is_array($value) ? self::Encode($value) : $value
					);
				break;
			case 1 :
				$node = array_shift($arguments);
				$return = isset($this->polylines[$node]) ? $this->polylines[$node] : array( 'points' => null, 'encoded' => null );
				break;
		}
		return $return;
	}
	
	/**
	 * Retreive list of polyline within singleton
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
	 * @return string $encoded_string
	 */
	final public static function Encode($points) {
		$points = self::flatten($points);
		$encoded_string = '';
		$index = 0;
		$previous = array(0,0);
		foreach($points as $number) {
			$number = (float)($number);
			$number = floor($number * 1e5);
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
	 * @return array $points
	 */
	final public static function Decode($string) {
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
			$points[] = $number * 1e-5;
		}
		return $points;
	}
	
	/**
	 * Reduce multi-dimensional to single list
	 *
	 * @param array $array
	 * @return array $flatten
	 */
	final public static function flatten($array) {
		$flatten = array();
		foreach(array_values($array) as $node) {
			if (is_array($node)) {
				$flatten = array_merge($flatten,self::flatten($node));
			} else {
				$flatten[] = $node;
			}
		}
		return $flatten;
	}
}
