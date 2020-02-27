<?php

namespace BlackFox2;

/**
 * Trait Instance
 * Instance is a direct synonym for singleton.
 * Unlike singleton, this pattern does not prohibit the creation of new objects of the same class.
 * In most cases, the project requires the same immutable global object:
 * ```php
 * ClassName::I()->Method();
 * ```
 *
 * In other cases, if necessary, it remains possible to create personal local mutable objects:
 * ```php
 * $ClassName = ClassName::N();
 * $ClassName->Method();
 * ```
 *
 * It is recommended that when designing a class, implement immutability by analyzing the flag $this->instanced.
 */
trait Instance {

	public static function AddLinks($links) {
		foreach ($links as $key => $Object) {
			self::$links[$key] = $Object;
		}
	}

	/** @var array */
	private static $links = [];

	/** @var array array of instantiated classes */
	private static $instances = [];

	/** @var bool if the class has been instanced - in most cases it is required to prohibit a change in its internal state */
	public $instanced = false;

	/**
	 * Returns the object being instantiated:
	 * - if the object has already been created - returns it
	 * - if the object has not yet been created - creates and returns it
	 *
	 * ```php
	 * ClassName::I()->Method();
	 * ```
	 *
	 * @return static object being instantiated
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function I() {
		$class = get_called_class();
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = self::N();
			self::$instances[$class]->instanced = true;
		}
		return self::$instances[$class];
	}

	/**
	 * Creates and returns a new instance of this class,
	 * filling all __construct parameters with values from self::$links
	 *
	 * ```php
	 * $ClassName = ClassName::N();
	 * $ClassName->Method();
	 * ```
	 *
	 * @return static object
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function N() {
		$ReflectionClass = new \ReflectionClass(get_called_class());
		$Parameters = $ReflectionClass->getMethod('__construct')->getParameters();

		$args = [];
		foreach ($Parameters as $Parameter) {
			if (!$Parameter->hasType()) {
				throw new Exception("Construct parameter '{$Parameter->getName()}' must have a type");
			}
			$type_name = $Parameter->getType()->getName();
			if (!isset(self::$links[$type_name])) {
				throw new Exception("Construct parameter '{$Parameter->getName()}' with type '{$type_name}' can't be matched with default links");
			}
			$args[$type_name] = self::$links[$type_name];
		}
		return $ReflectionClass->newInstanceArgs($args);
	}
}