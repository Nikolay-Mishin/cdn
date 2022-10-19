<?php
// трейт для создания единственного экземляра класса (если он уже создан обращение идет к уже созданному экземпляру)
// трейт (trait) (кусок кода, который мы можем копипастить в другие файлы)
// реализаванный в трейте код вставляется в месте, где он задействован (подключен)

namespace ishop\traits;

trait T_GetContents {

    public static function getFileContents(string $file, array $vars = []): string {
        extract($vars); // извлекаем данные из массива и сформируем из них соответствующие переменные
        ob_start(); // включаем буферизацию
        require_once $file; // подключаем шаблон
        return ob_get_clean(); // получаем контент из буфера и очищаем буфер
    }

    public function getContents(string $file, array $vars = []): string {
        extract($vars); // извлекаем данные из массива и сформируем из них соответствующие переменные
        ob_start(); // включаем буферизацию
        require_once $file; // подключаем шаблон
        return ob_get_clean(); // получаем контент из буфера и очищаем буфер
    }

}
