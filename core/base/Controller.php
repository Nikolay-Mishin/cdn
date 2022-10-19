<?php
// базовый класс контроллера фреймворка - описывает базовые свойства и методы, которые будут наследоваться всеми контроллерами
// работает с данными и видами - обработка полученных из определенного контроллера/модели данных и отображение вида (view) 
namespace ishop\base;

use ishop\Db; // класс БД

abstract class Controller {

    use \ishop\traits\T_Ajax;
    
    public array $route; // массив с маршрутами
    public string $controller; // контроллер
    public string $model; // модель
    public string $view; // вид
    public string $prefix; // префикс
    public string $layout = ''; // шаблон
    public array $data = []; // обычные данные (контент)
    // мета-данные (по умолчанию пустые значения для индексов)
    public array $meta = ['title' => '', 'desc' => '', 'keywords' => '']; 

    public string $canonical = ''; // каноническая ссылка
    public string $file_prefix = ''; // префикс
    public array $style = ['lib' => [], 'main' => [], 'add' => []];
    public array $script = ['lib' => [], 'init' => [], 'main' => [], 'add' => []];

    public function __construct(array $route) {
        if (!CUSTOM_DB_INSTANCE) Db::instance(); // создаем объект класса БД

        $this->route = $route;
        $this->controller = $route['controller'];
        $this->model = $route['controller'];
        $this->view = $route['action'];
		$this->prefix = $route['prefix'];

        // если жёстко передано значение false (подключение шаблона выключено - например, когда контент передан ajax-запросом)
		// если передан какой-то шаблон, то берем его, иначе значение константы LAYOUT
        $this->layout = $this->layout === false ? false : ($this->layout ?: LAYOUT);

        $this->file_prefix = rtrim($this->prefix, '\\') . ($this->prefix ? '_' : '');
        $this->setStyle();
        $this->setScript();
    }

    // получает объект вида и вызывает рендер
    public function getView(): void {
        $viewObject = new View($this /*, $this->route, $this->layout, $this->view, $this->meta*/); // объект класса Вида
        $viewObject->render($this->data); // вызов метода для рендера и передаем данные из контроллера в вид
    }

    // возвращает html-ответ (шаблон/вид) на ajax-запрос
    // внутри отдельного шаблона доступны переменные метода и объект класса ($this - Menu, Filter), где подключается шаблон
    // в видах, подключаемых через loadView() доступен объект класса, в котором был вызван метод ($this - CartController)
    // в видах, подключаемых через класс вида доступен объект класса View ($this - View)
    public function loadView(string $view, array $vars = []): void {
        extract($vars); // извлекаем переменные из массива
        require APP . "/views/{$this->prefix}{$this->controller}/{$view}.php"; // подключаем вид
        die;
    }

    // определяет, каким видом пришел запрос (асинхронно/ajax или нет)
    //public function isAjax() {
    //    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    //}

    // записывает полученные данные в массив (свойство)
    public function set(array $data): void {
        $this->data = $data;
    }

    // задает массив мета-данных
    public function setMeta(string $title = '', string $desc = '', string $keywords = ''): void {
        $this->meta['title'] = h($title); // заголовок
        $this->meta['desc'] = h($desc); // описание
        $this->meta['keywords'] = h($keywords); // ключевые слова
    }

    // задает каноническую ссылку
    public function setCanonical(string $url = ''): void {
        $this->canonical = h($url); // каноническая ссылка
    }

    public function setStyle(string ...$styles): void {
        $this->setFiles('style', ...$styles); // подключаем файл конфигурации стилей
    }

    public function setScript(string ...$scripts): void {
        $this->setFiles('script', ...$scripts); // подключаем файл конфигурации скриптов
        
    }

    public function setFiles(string $type, string ...$files): void {
        $file = require_once CONF . "/require/{$this->file_prefix}{$type}.php"; // подключаем файл конфигурации
        if ($file !== true) foreach ($file as $file_type => $file_list) $this->$type[$file_type] = $file_list;
        foreach ($files as $file) $this->$type['add'][] = $file;
		//if ($file !== true) {
		//    foreach ($file as $file_type => $file_list) {
		//        $this->$type[$file_type] = $file_list;
		//    }
		//}
		//foreach ($files as $file) {
		//    $this->$type['add'][] = $file;
		//}
    }

}
