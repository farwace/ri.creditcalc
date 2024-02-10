<?php

namespace RI\CreditCalc;


trait Singleton {
	private static $instance;

	/** @var Config */
	protected $config;

	private function __construct(){
		$this->config = Config::getInstance();
	}

	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new static();
			if(method_exists(self::$instance, 'createInstance')){
				self::$instance->createInstance();
			}
		}
		return self::$instance;
	}
}
