<?php
// базовый класс вида фреймворка - описывает базовые свойства и методы, которые будут наследоваться всеми видами
// работает с видами - получение данных из контроллера и отображение контента страницы на основе шаблона и полученных данных

namespace ishop\base;

use \Exception;

use ishop\App;

class View {

	use \ishop\traits\T_SetProperties;
	use \ishop\traits\T_GetContents;

	public ?Controller $controllerObj = null;

	public array $route = [];
	public string $controller = '';
	public string $model = '';
	public string $view = '';
	public string $prefix = '';
	public string $layout = '';
	public array $data = [];
	public array $meta = []; // массив с мета-тегами, заданными через метод setMeta() в базовом контроллере

	public string $canonical = '';
	public array $style = [];
	public array $script = [];

	private array $masks = [
		'style' => '<link href="file.css" rel="stylesheet" type="text/css" media="all" />',
		'script' => '<script src="file.js"></script>'
	];

	public function __construct(?Controller $controller = null /*, array $route, string $layout = '', string $view = '', array $meta = []*/) {
		$this->setProperties([objectUnset(clone $this->controllerObj = $controller, 'data')]);
		//$this->setProperties([objectUnset(clone $controller, 'data')], function($k) use($controller) {
		//    // если жёстко передано значение false (подключение шаблона выключено - например, когда контент передан ajax-запросом)
		//    if ($k === 'layout') {
		//        if ($controller->$k === false) {
		//            $this->$k = false;
		//        }
		//        else {
		//            // если передан какой-то шаблон, то берем его, иначе значение константы LAYOUT
		//            $this->$k = $controller->$k ?: LAYOUT;
		//        }
		//    }
		//});

		//$layout - шаблон для отображения (обертка над видом - статичные части сайта - меню, сайдбар, футер и тд)
		//$view - вид для отображения

		//$this->route = $route;
		//$this->controller = $route['controller'];
		//$this->view = $view;
		//$this->model = $route['controller'];
		//$this->prefix = $route['prefix'];
		//$this->meta = $meta;
		//// если жёстко передано значение false (подключение шаблона выключено - например, когда контент передан ajax-запросом)
		//if ($layout === false) {
		//    $this->layout = false;
		//}
		//else {
		//    // если передан какой-то шаблон, то берем его, иначе значение константы LAYOUT
		//    $this->layout = $layout ?: LAYOUT;
		//}
	}

	// получает опции
	//protected function setOptions($controller, $callback = null) {
	//    $callback = $callback ?? function(){};
	//    // если в свойствах класс существует ключ из переданных настроек, то заполняем данное свойство переданным значением
	//    foreach ($controller as $k => $v) {
	//        // проверяем существет ли такое свойство у класса
	//        if (property_exists($this, $k)) {
	//            $this->$k = $v;
	//            $callback($controller, $k);
	//        }
	//    }
	//}

	// рендерит (формирует) страницу для пользователя на основе полученных данных
	public function render(array $data): void {
		//если $data - массив, то извлечем данные из массива и сформируем из них соответствующие переменные
		//extract($data);
		//prefix - имя префикса (админки)
		//controller - имя папки, в которой лежат соответствующие вызванному контроллеру виды
		//view - имя вида, который должен быть отображен
		$this->prefix = str_replace('\\', '/', $this->prefix);
		$viewFile = APP . "/views/{$this->prefix}{$this->controller}/{$this->view}.php";
		// если такой файл существует - подключаем его, иначе выбрасываем исключение - такой вид не найден
		if (is_file($viewFile)) {
			$content = $this->getContents($viewFile, $data); // получаем контент из буфера
		}
		else {
			throw new Exception("Не найден вид {$viewFile}", 500);
		}
		// если свойство layout не равно false (подключение шаблона включено)
		if (false !== $this->layout) {
			$layoutFile = APP . "/views/layouts/{$this->layout}.php"; // путь к файлу шаблона
			// если такой файл существует - подключаем его, иначе выбрасываем исключение - такой шаблон не найден
			if (is_file($layoutFile)) {
				require_once $layoutFile;
			}
			else {
				throw new Exception("Не найден шаблон {$this->layout}", 500);
			}
		}
	}

	// возвращает готовую разметку (или массив) с мета-тегами (title, description, keywords)
	public function getMeta(): string {
		// разметку для вывода в шаблон запишем в переменную и вернем ее
		$output = "<title>{$this->meta['title']}</title>".PHP_EOL; // PHP_EOL - перенос строк
		$output .= "<meta name='description' content={$this->meta['desc']}>".PHP_EOL;
		$output .= "<meta name='keywords' content={$this->meta['keywords']}>".PHP_EOL;
		return $output;
	}

	// возвращает готовую разметку с канонической ссылкой
	public function getCanonical(): string {
		// разметку для вывода в шаблон запишем в переменную и вернем ее
		// PHP_EOL - перенос строк
		return $this->canonical ? "<link rel='canonical' href='{$this->canonical}'>" : '';
	}

	// возвращает готовую разметку (или массив) со стилями
	public function getStyles(): string  {
		return $this->getFilesList($this->style, 'style');
	}

	// возвращает готовую разметку (или массив) со стилями
	public function getScripts(): string  {
		return App::getConsts(CONF."/require/{$this->controllerObj->file_prefix}consts.php").PHP_EOL.
			$this->getFilesList($this->script, 'script');
	}

	protected function getFilesList(array $files, string $type_file): string {
		$files_list = '';
		$mask = $this->masks[$type_file];
		$prefix = $type_file == 'style' ? 'css/' : 'js/';
		foreach ($files as $type => $file_list) {
			$files_list .= "<!-- $type -->".PHP_EOL;
			$file_list = toArray($file_list);
			foreach ($file_list as $file) {
				$files_list .= $this->checkFilePath($file, $mask, $prefix).PHP_EOL;
			}
		}
		return $files_list;
	}

	protected function checkFilePath(string $file, string $mask, string $prefix = ''): string {
		if (preg_match('/^(@\s*)(.+)$/', $file, $match)) {
			return require_once $match[2];
		}
		elseif (preg_match('/^(\/)(.+)$/', $file, $match)) {
			return $this->maskReplace($match[2], $mask);
		}
		return $this->maskReplace($file, $mask, $prefix);
	}

	protected function maskReplace(string|array $str, string $mask, string $prefix = '', string|array $search = 'file'): string|array|null {
		return call_user_func_array(isRegex($search) ? 'preg_replace' : 'str_replace', [$search, $prefix.$str, $mask]);
	}

}
