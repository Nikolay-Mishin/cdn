<?php

namespace ishop\base;

use ishop\libs\Parser;

/**
* Базовая Фабрика, которую будут наследовать все Фабрики
*
* @author [x26]VOLAND
*/
abstract class Factory {

	protected static string $postfix = '';
	protected static string $extends = '';
	protected static string $implements = '';
	protected static string $context = '';
    
	/**
	* Создаёт экземпляр класса заданного типа.
	*
	* @param string $type Тип коллекции
	* @param mixed $args Объекты
	* @return object
	*/
	public static function create(string|object $type, mixed ...$args): object {
	    if (self::checkType($type)) array_unshift($args, $type);
	    return self::getInstance($type, ...$args);
	}
  
	protected static function checkType(string|object $type): bool {
	    return is_object($type) || !is_string($type);
	}

	/**
	* Создаёт экземпляр класса заданного типа.
	*
	* @param string $type Тип коллекции
	* @param mixed $args Объекты
	* @return object
	*/
	protected static function getInstance(string|object $type, mixed ...$args): object {
	    extract(self::createClass($type));
	    return new $class($type, ...$args);
	}

	/**
	* Создаёт класс с именем $class
	*
	* @param string $class Имя класса
	* @return void
	*/
	protected static function createClass(string|object $class): array {
	    extract(Parser::_namespace($class, static::$postfix));
	    if (!class_exists($class)) {
	        $_namespace = $namespace ? "namespace $namespace;" : $namespace;
	        $extends = static::$extends ? "extends ".static::$extends : static::$extends;
	        $implements = static::$implements ? "implements ".static::$implements : static::$implements;
	        eval("$_namespace class $className $extends $implements { ".static::$context." }");
	    }
	    return compact('type', 'class');
	}

}
