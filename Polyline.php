<?php

class Polyline {
	private $shapes = array();
	private static $instance;
	private function __constuct() {
		
	} 

	public static function getInstance() {
		return self::$instance instanceof self ? self::$instance : self::$instance = new self;
	}

	public function __get($node) {
		$return = null;
		if (preg_match('/^(.*?)(points|encoded)$/i',$node,$matches)) {
			list($all,$key,$type) = $matches;
			if(isset($this->shapes[$key]) && isset($this->shapes[$key][$type])) {
				$type = strtolower($type);
				$return = $this->shapes[$key][$type];
			} else {
				$return = $type == 'points' ? array() : '';
			}
		} elseif (isset($this->shapes[$node])) {
			$return = $this->shapes[$node];
		} else {
			throw BadMethodException();
		}
		return $return;
	}
	
	public function __set($node,$value=array()) {
		$this->shapes[$node] = array(
				'points'  => is_array($value) ? self::flatten($value) : $this->decode($value),
				'encoded' => is_array($value) ? $this->encode($value) : $value
			);
		return $value;
	}
	
	public function __call($method,$agruments) {
	}

	/**
	 * Apply Google Polyline algorithm to list of points
	 *
	 * @param array $points
	 * @return string $encoded_string
	 */
	public function encode($points) {
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
	public function decode($string) {
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
	public static function flatten($array) {
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
