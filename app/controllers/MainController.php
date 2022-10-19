<?php

namespace app\controllers;

class MainController extends AppController {

    // главная страница
    public function indexAction(): void {
        // SELECT `brand`.*  FROM `brand` LIMIT 3
        $brands = \R::find('brand', 'LIMIT 3'); // выбираем из таблицы brand первые 3 записи
        // выбираем 8 записей из таблицы product, которые являются хитами (hit = '1') и имеют статус 'отображать' (status = '1')
        // enum('0','1') - строковый тип
        // SELECT `product`.*  FROM `product`  WHERE hit = '1' AND status = '1' LIMIT 8
        $hits = \R::find('product', "hit = '1' AND status = '1' LIMIT 8");
        // $posts = \R::findAll('test'); // получаем все статьи из таблицы test
        // биндим данные (защита от sql-инъекций) вместо '?' подставляются указанные данные
        // $post = \R::findOne('test', 'id = ?', [2]);

        // echo __METHOD__; // константа имя метода (app\controllers\MainController::indexAction)
        // $this->layout = 'test'; // меняем свойство layout для данного контроллера (подключаемый шалон)
        
        // заполняем мета-данные для данного контроллера
        // 'Главная страница' - можно использовать App::$app->getProperty('shop_name') | use ishop\App
        $this->setMeta('Главная страница', 'Описание...', 'Ключевики...');
        $this->setCanonical(PATH);
        
        // заполняем данные для данного контроллера (передаем массив с данными)
        // ['name' => 'John', 'age' => 30, 'names' => ['Andrey', 'Jane',]]
        // compact - создает массив из переданных переменных (имен) по типу ключ-значение
        $this->set(compact('brands', 'hits'));
        
        // данные для данного контроллера
        /* $name = 'John';
        $age = 30;
        $names = ['Andrey', 'Jane', 'Mike'];

        // Cache::set('test', $names); // кэшируем данные
        // Cache::delete('test'); // очищаем кэш
        // $data = Cache::get('test'); // получаем данные из кэша
        // если данные не получены, то кэшируем их заново
        /* if (!$data) {
            Cache::set('test', $names);
        } */
        // debug($data); // распечатываем массив с данными кэша

    }

}
