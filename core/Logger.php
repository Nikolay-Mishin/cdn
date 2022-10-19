<?php

namespace ishop;

class Logger {

    public static function getLog(): void {
        // выводим все запросы выполняемые RedBeanPHP
        $logs = \R::getDatabaseAdapter()->getDatabase()->getLogger();
        debug($logs->grep('SELECT'));
        // распечатка массива с логом SQL запросов
        /*
        Array
        (
            [0] => SELECT code, title, symbol_left, symbol_right, value, base FROM currency ORDER BY base DESC
            [1] => SELECT `product`.*  FROM `product`  WHERE alias = ? AND status = '1' LIMIT 1  -- keep-cache
            [2] => SELECT * FROM related_product JOIN product ON product.id = related_product.related_id WHERE related_product.product_id = ?
            [3] => SELECT `product`.*  FROM `product`  WHERE id IN (?,?,?) LIMIT 3 -- keep-cache
            [4] => SELECT `gallery`.*  FROM `gallery`  WHERE product_id = ? -- keep-cache
        )
        */
    }

}
