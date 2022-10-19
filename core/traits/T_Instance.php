<?php
// трейт для создания единственного экземляра класса (если он уже создан обращение идет к уже созданному экземпляру)
// трейт (trait) (кусок кода, который мы можем копипастить в другие файлы)
// реализаванный в трейте код вставляется в месте, где он задействован (подключен)

namespace ishop\traits;

trait T_Instance {

    protected static ?self $instance = null; // хранит экземпляр класса

    public function __construct() {}

    // если свойство текущего класса не инициализировано (пусто), то в него записываем объект данного класса и вернем его
    public static function instance(): static {
        return static::$instance ?? static::getInstance();
    }

    protected static function getInstance(): static {
        return static::$instance = new static;
	}

}
