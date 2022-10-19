<?php
// трейт для создания единственного экземляра класса (если он уже создан обращение идет к уже созданному экземпляру)
// трейт (trait) (кусок кода, который мы можем копипастить в другие файлы)
// реализаванный в трейте код вставляется в месте, где он задействован (подключен)

namespace ishop\traits;

use \Exception;
use \Closure;
use \Bean;

trait T_Protect {

	protected array $protectProperties = [];
	protected array $protectMethods = [];

	public function __get(string $property): mixed {
		return $this->exist($property, function(object $obj, string $property) {
			return $obj->$property;
		});
	}

	public function __set(string $property, mixed $value): void {
		$this->exist($property, function(object $obj, string $property, bool $const, string $class) use($value) {
			!$const ? $obj->$property = $value : $this->getException(500, "Изменение значения свойства $class::$property");
		});
	}

	public function __call(string $method, array $args): mixed {
		return $this->exist($method, function(object $obj, string $method, $const, string $class, bool $isBean, bool $exist) use($args) {
			//debug(['class' => getClass($obj), 'method' => $method, 'isBean' => $isBean, 'exist' => $exist]);
			return $isBean && $exist ? call_user_func_array([$obj, $method], $args) : callPrivateMethod($obj, $method, $args);
		}, 'protectMethods');
	}

	public function getProtectProperties(): array {
		return $this->protectProperties;
	}

	public function getProtectMethods(): array {
		return $this->protectMethods;
	}

	protected function setProtectProperties(string ...$protectProperties): void {
		$this->protectProperties = [];
		$this->addProtectProperties(...$protectProperties);
	}

	protected function addProtectProperties(string ...$protectProperties): void {
		$this->structuredProtect($protectProperties);
	}

	protected function setProtectMethods(string ...$protectMethods): void {
		$this->protectMethods = [];
		$this->addProtectMethods(...$protectMethods);
	}

	protected function addProtectMethods(string ...$protectMethods): void {
		$this->structuredProtect($protectMethods, 'protectMethods');
	}

	private function structuredProtect(array|string $protectProperties, string $protectList = 'protectProperties', bool $new = true): void {
		if ($new) $this->structuredProtect($this->$protectList, $protectList, false);
		//debug(["$protectList - start" => $this->$protectList]);
		foreach ($protectProperties as $property => $mod) {
			//debug(['class' => getClass($this), $property => $mod]);
			$this->reverseProtectProperty($property, $mod, $protectList, $new);
		}
		//debug(["$protectList - end" => $this->$protectList]);
	}

	private function reverseProtectProperty(string $property, string $mod, string $protectList, bool $new): void {
		list($isConst, $isMethod) = [(int) $property == $property, $this->isMethod($protectList)];
		$reverse = $new ? !$isMethod && $isConst && !array_key_exists($property, $this->$protectList) : !$isMethod && $isConst;
		list($key, $property) = [$property, $isConst ? $mod : $property];
		//debug(['new' => $new, 'reverse' => $reverse, 'property' => $property, 'mod' => $mod, 'protectList' => $protectList]);
		if ($reverse) {
			$property = preg_replace('/=>|=/', ',', str_replace(' ', '', $property));
			$explode = explode(',', $property);
			list($property, $mod) = [$explode[0], isset($explode[1]) && $explode[1] == 'set' ? 'set' : 'get'];
		}
		if ($exist = $this->getExists($protectList, $property)) {
			if(!$isMethod) $this->$protectList[$property] = $mod;
			if($isMethod && $new) $this->$protectList[] = $property;
		}
		$key = !$exist && !$isMethod ? $property : $key;
		if (!$new && !$exist) arrayUnset($this->$protectList, $key);
		//debug([$mod => $property, 'isMethod' => $isMethod, 'key' => $key, 'exist' => $exist, 'del' => !$new && !$exist]);
	}

	private function isMethod(string $protectList): bool {
		return $protectList == 'protectMethods';
	}

	private function getExists(string $protectList, string $property, ?object $obj = null) {
		$exist = $this->isMethod($protectList) ? 'isCallable' : 'property_exists';
		return call_user_func_array($exist, [$obj ?? $this, $property]);
	}

	private function exist(string $property, Closure $callback, string $protectList = 'protectProperties'): mixed {
		$this->structuredProtect($this->$protectList, $protectList, false);
		$class = getClass($this);
		$error = ($this->isMethod($protectList) ? 'Метод' : 'Свойство') . " $class::$property";
		list($_property, $exist, $access) = $this->propertyExist($this, $property, $callback, $protectList);
		//debug(['_property' => $_property, 'exist' => $exist, 'access' => $access, 'type' => gettype($_property)]);
		if (!$exist || !$access) {
			$this->getException(500, $error, $exist, $access, $this->isMethod($protectList));
		}
		return $_property;
	}

	private function propertyExist(object $obj, string $property, Closure $callback, string $protectList = 'protectProperties', bool $inProperty = false): ?array {
		if (is_object($obj)) return null;

		$exist = $this->getExists($protectList, $property, $obj);
		list($isBean, $isMethod) = [$this->isBean($obj), $this->isMethod($protectList)];
		$propertyExist = $exist || ($isBean && array_key_exists($property, $obj->getProperties()));
		$inProtectList = $isMethod ? 'in_array' : 'array_key_exists';
		$access = $isBean || $inProperty ?: $inProtectList($property, $this->$protectList);

		//debug(['$property' => $this->$protectList[$property]]);
		//debug([
		//    'class' => getClass($obj), 'property' => $property, 'inProperty' => $inProperty, 'protectList' => $protectList,
		//    'isMethod' => $isMethod, 'isBean' => $isBean, 'List' => $this->$protectList, 
		//    'propertyExist' => $propertyExist, 'access' => $access, 'else' => !$access && !$isBean && !$inProperty
		//]);

		if ($propertyExist && $access) {
			$isConst = ($this->$protectList[$property] ?? 'get') === 'get';
			
			$_property = $callback($obj, $property, $isConst, getClass($obj), $isBean, $exist);
		}
		elseif (!$access && !$isBean && !$inProperty) {
			//debug(['protectProperties' => $this->protectProperties]);
			foreach ($this->protectProperties as $protect => $mod) {
				//debug([$protect => $mod]);
				$result = $this->propertyExist($this->$protect, $property, $callback, $protectList, true);
				$_property = $result[0];
				if ($result[1]) list($propertyExist, $access) = [$result[1], $result[2]];
				//debug(['propertyExist' => $propertyExist, 'access' => $access, 'result' => $result]);
				if ($propertyExist && $access) break;
			}
		}
		return [$_property ?? null, $propertyExist, $access];
	}

	private function isBean(object $obj): bool {
		return $obj instanceof Bean;
	}

	private function getException(int $code, string $error, bool $exist = true, bool $access = true, bool $isMethod = false): void {
		$context = getContext();
		$msg = $exist && !$access ? "$context->class::$context->function (строка $context->line)" : '';
		$msg = $msg ? "недоступ" . ($isMethod ? 'ен' : 'но') . " в области видимости $msg" : "отсутствует в объекте";
		throw new Exception("$error $msg", $code);
	}

}
