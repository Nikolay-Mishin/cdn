<?php
// класс ядра приложения
// реализует паттерн (шаблон проектирования) Реестр (Registry)

// namespace (пространство имен) - путь к данному классу относительно корня приложения
// PSR-4 - стандарт именования классов/скриптов и прочего (стандарт написания кода)
// абсолютное имя класса - \<ИмяПространстваИмён>(\<ИменаПодпространствИмён>)*\<ИмяКласса>
// ИмяПространстваИмён (vendor name) - имя производителя (разработчика/продукта) - виртуальная папка, которой в проекте нет
// (vendor name) ishop = vendor/ishop/core (псевдоним пути в автозагрузчике - composer.json)
// app = app

namespace ishop;

class App {

	public static Registry $app; // контейнер (реестр) для приложения (хранение свойств/объектов)
	public static string $controller;
	public static string $action;

	public function __construct() {
		// отсекаем концевой '/' строки запроса (после доменного имени http://ishop2.loc/)
		$query = trim($_SERVER['QUERY_STRING'], '/');
		session_start(); // стартуем сессию
		self::$app = Registry::instance(); // запишем в свойство приложения объект Реестра
		$this->getParams(); // получаем параметры приложения
		new ErrorHandler(); // создаем объект класса Исключений
		Router::dispatch($query); // передаем в маршрутизатор для обработки запрошенный url адрес
	}

	public static function getConsts(array|string $vars = [], bool $categorize = true, string $part = 'user'): string {
		/**
		* Метод Object.freeze() замораживает объект: это значит, что он предотвращает добавление новых свойств к объекту, удаление старых свойств из объекта и изменение существующих свойств или значения их атрибутов перечисляемости, настраиваемости и записываемости. В сущности, объект становится эффективно неизменным. Метод возвращает замороженный объект.
		*/
		//debug(varName($vars));
		$project = self::$app->getProperty('project');
		$consts = json_encode(get_defined_constants($categorize)[$part]);
		$vars = json_encode(is_array($vars) ? $vars : require_once($vars));
		return "<!-- _variables - ряд основных переменных, которые будут использоваться в главном скрипте -->
		<script>
			const $project = {
				Consts: $consts,
				Vars: $vars
			};
			Object.freeze($project); // замораживает объект
			Object.freeze($project.consts); // замораживает объект
			Object.freeze($project.vars); // замораживает объект
		</script>";
	}

	protected function getParams(): void {
		$params = require_once CONF . '/params.php'; // массив параметров (настроек) приложения
		// записываем каждый из параметров в реестр
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				self::$app->setProperty($k, $v);
			}
		}
	}

}
