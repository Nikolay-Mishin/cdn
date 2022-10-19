<?php
// базовый класс Контроллеров приложения, который наследуют остальные контроллеры

namespace app\controllers;

use app\models\AppModel; // подключаем базовый класс Моделей приложения
use app\models\Cart; // модель корзины
use app\widgets\currency\Currency; // подключаем виджет валюты
use ishop\App; // подключаем класс базовый приложения
use ishop\base\Controller; // подключаем базовый класс Контроллера фреймворка
use ishop\Cache; // подключаем класс кэша

class AppController extends Controller {

    public function __construct(array $route) {
        // перегрузка - переопределение методов и свойств родительского класса
        parent::__construct($route); // вызов родительского конструктора, чтобы его не затереть (перегрузка методов и свойств)
        if (CUSTOM_DB_INSTANCE) new AppModel(); // создаем объект базовой модели приложения
        // записываем в контейнер (реестр) список доступных валют и текущую валюту
        App::$app->setProperty('currencies', Currency::getCurrencies());
        $curr = Currency::getCurrency(App::$app->getProperty('currencies')); // получаем текущую валюту из реестра
        App::$app->setProperty('currency', $curr);
        App::$app->setProperty('cats', self::cacheCategory()); // записываем категории в контейнер
        // проверем изменение текущей валюты
        if (Currency::checkChangeCurrency($curr)) {
            Cart::recalc($curr); // вызываем метод пересчита корзины
        }
    }

    // метод для кэширования массива категорий
    public static function cacheCategory(): array {
        $cats = Cache::get('cats'); // получаем категории из кэша
        // если данные из кэша не получены
        if (!$cats) {
            // SELECT * FROM category
            $cats = \R::getAssoc("SELECT * FROM category"); // берем категории из БД
            Cache::set('cats', $cats); // записываем в кэш
        }
        return $cats;
    }

}
