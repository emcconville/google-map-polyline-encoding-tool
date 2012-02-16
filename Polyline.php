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
	}
	
	public function __set($node,$value=array()) {
	}
	
	public function __call($method,$agruments) {
	}

	public function encode($points) {
	}
	
	public function decode($string) {
	}
}
