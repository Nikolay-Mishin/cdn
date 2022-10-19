<?php
// класс Маршрутизатор - отвечает за обработку запросов (url) и вызов контроллера (controller) и экшена (action)

namespace ishop;

use \Exception;

class Router {
    // таблица марштуров
    protected static array $routes = [];
    // текущий маршрут - если найдено соответствие с адресом в маршрутах
    protected static array $route = [];

    // записывает правила в таблицу маршрутов
    public static function add(string $regexp, array $route = []): void {
        // $regexp - шаблон адреса
        // $route (не обязателен) - конкретный контроллер/экшен, который будет соответствовать шаблону
        self::$routes[$regexp] = $route;
    }

    // возвращает таблицу маршрутов
    public static function getRoutes(): array {
        return self::$routes;
    }

    // возвращает тукещий маршрут
    public static function getRoute(): array {
        return self::$route;
    }

    // принимает запрошенный url адрес и обрабатывает его
    public static function dispatch(string $url): void {
        $url = self::removeQueryString($url);
        // ищем соответствие переданного адреса в таблице маршрутов
        if (self::matchRoute($url)) {
            // имя контроллера (путь), который будет вызван + постфикс (MainController)
            $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
            // проверяем существует ли такой класс
            if (class_exists($controller)) {
                App::$controller = $controller;
                $controllerObject = new $controller(self::$route); // объект вызываемого контроллера
                $action = self::lowerCamelCase(self::$route['action']) . 'Action'; // задаем имя экшена для вызова (indexAction)
                App::$action = $action;
                // проверяем существует ли такой метод у данного класса
                if (method_exists($controllerObject, $action)) {
                    $controllerObject->$action(); // вызываем экшен у заданного класса
                    $controllerObject->getView(); // вызываем метод для отображения вида
                }
                else {
                    throw new Exception("Метод $controller::$action не найден", 404);
                }
            }
            else {
                throw new Exception("Контроллер $controller не найден", 404);
            }
        }
        else {
            throw new Exception("Страница не найдена", 404);
        }
    }

    // принимает запрошенный url адрес и ищет соответствие в маршрутах
    public static function matchRoute(string $url): bool {
        foreach (self::$routes as $pattern => $route) {
            // ищем совпадения шаблона $pattern в $url и записываем результат в $matches
            if (preg_match("#{$pattern}#", $url, $matches)) {
                foreach ($matches as $k => $v) {
                    // если ключ - строка, записываем в массив $route ключ-значение из $matches
                    if (is_string($k)) {
                        $route[$k] = $v;
                    }
                }
                // action по умолчанию = index
                if (empty($route['action'])) {
                    $route['action'] = 'index';
                }
                // prefix по умолчанию = ''
                if (!isset($route['prefix'])) {
                    $route['prefix'] = '';
                }
                else {
                    $route['prefix'] .= '\\'; // добавляем обратный слэш (2 слэш - экранирование)
                }
                $route['controller'] = self::upperCamelCase($route['controller']); // меняем имя контроллера для вызова
                self::$route = $route; // записываем результат в текущий маршрут
                return true;
            }
        }
        return false;
    }

    // CamelCase - для изменения имен контроллеров (каждое слово в верхнем регистре)
    protected static function upperCamelCase(string $name): string {
        // page-new => page new => Page New => PageNew
        // - заменяем на пробел => ucwords - каждое слово делает с заглавной буквы => заменяем пробел на пустую строку
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }

    // camelCase - для изменения имен экшенов (первый символ в нижнем регистре)
    protected static function lowerCamelCase(string $name): string {
        return lcfirst(self::upperCamelCase($name));
    }

    // вырезает из строки запроса (url) get-параметры (ishop/?id=1 => '&id=1')
    protected static function removeQueryString(string $url): string {
        if ($url) {
            $params = explode('&', $url, 2); // разделяем строку по символу '&' и возвращаем массив не более, чем из 2 элементов
            // page/view/?id=1&page=1
            // 0 = page/view/
            // 1 = id=1&page=1
            // если в 0 элементе массива нет знака '=' возвращаем данную, иначе возвращаем пустую строку
            if (false === strpos($params[0], '=')) {
                return rtrim($params[0], '/'); // вырезаем из конца строки '/'
            }
            return '';
        }
        return '';
    }

}
