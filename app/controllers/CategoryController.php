<?php
// Контроллер категорий

namespace app\controllers;

use \Exception;

use app\models\Category; // модель категорий
use app\models\Breadcrumbs; // модель хлебных крошек
use ishop\App;
use ishop\libs\Pagination; // класс пагинации (постраничной навигации)
use app\widgets\filter\Filter; // виджет фильтров

class CategoryController extends AppController {

    public function viewAction(): void {
        $alias = $this->route['alias']; // алиас запрошенной категории
        // SELECT `category`.*  FROM `category`  WHERE alias = ? LIMIT 1
        $category = \R::findOne('category', 'alias = ?', [$alias]); // получаем категорию из БД
        // если категория не получаена выбрасываем исключение
        if (!$category) {
            throw new Exception('Страница не найдена', 404);
        }

        // хлебные крошки
        $breadcrumbs = Breadcrumbs::getBreadcrumbs($category->id); // хлебные крошки;

        $cat_model = new Category(); // модель категорий
        $ids = $cat_model->getIds($category->id); // получаем список id вложенных категорий (дочерних к запрошенной)
        // если вложенных категорий нет берем запрошенную категорию, иначе к вложенным категориям добавляем запрошенную
        $ids = !$ids ? $category->id : $ids . $category->id; // 4,6,7,5,8,9,10,1 (для категории 1)

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // текущая страница
        $perpage = App::$app->getProperty('pagination'); // получаем из контейнера число записей для вывода на 1 странице
        
        // простейший вариант фильтрации - когда у товара есть хотя бы 1 из заданных аттрибутов
        // товар 1-3 имеют 2 аттрибута (1,5) товар 4 имеет 1 аттрибут (5), но не имеет аттрибута (1) => товар 1-4
        // сложный фильтр - отображает только товары со вмесем фильтрами (1,5) => товар 1-3
        // формируем sql-запрос выборки товаров по фильтрам, если передан get-параметр 'filter'
        $sql_part = '';
        if (!empty($_GET['filter'])) {
            // выбираем товары из категории 6 и во 2 выборку IN идет вложенный sql-запрос
            // для товаров attribute_product мы выбираем те товары, для которых поле attr_id = 1,5
            $filter = Filter::getFilter(); // получаем список выбранных фильтров
            if ($filter) {
                $cnt = Filter::getCountGroups($filter); // получаем число групп
                // группируем результат выборки по полю product_id, таким образом получаем
                // AND id IN
                // (
                // SELECT product_id FROM attribute_product WHERE attr_id IN (1) GROUP BY product_id HAVING COUNT(product_id) = 1
                // )
                $sql_part = "AND id IN (SELECT product_id FROM attribute_product WHERE attr_id IN ($filter) GROUP BY product_id HAVING COUNT(product_id) = $cnt)";
            }
        }

        // получаем общее число товаров из списка полученных категорий
        // SELECT COUNT(*) FROM `product`  WHERE category_id IN (4,6,7,5,8,9,10,1)
        // AND id IN (SELECT product_id FROM attribute_product WHERE attr_id IN (1))
        $total = \R::count('product', "category_id IN ($ids) $sql_part");
        $pagination = new Pagination($page, $perpage, $total); // объект пагинации
        $start = $pagination->getStart(); // получаем номер записи, с которой необходимо начинать выборку из БД

        // товары списка полученных категория (IN ищет совпадение в заданном диапазоне - 4,6,7,5,8,9,10,1)
        // не биндим значение ("category_id IN ?", [$ids]), тк значение для выборки из БД мы формируем сами на основе данных из БД
        // SELECT `product`.*  FROM `product`  WHERE status = '1' AND category_id IN (4,6,7,5,8,9,10,1)
        // AND id IN
        // (
        // SELECT product_id FROM attribute_product WHERE attr_id IN (1,2) GROUP BY product_id HAVING COUNT(product_id) = 1
        // )
        // LIMIT 0, 3
        $products = \R::find('product', "status = '1' AND category_id IN ($ids) $sql_part LIMIT $start, $perpage");

        // если данные пришли ajax, загружаем вид фильтра и передаем соответствующие данные
        if ($this->isAjax()) {
            $this->loadView('filter', compact('products', 'total', 'pagination'));
        }

        $this->setMeta($category->title, $category->description, $category->keywords);
        $this->set(compact('products', 'breadcrumbs', 'pagination', 'total'));
    }

}
