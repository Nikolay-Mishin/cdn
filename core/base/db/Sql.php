<?php

namespace ishop\base\db;

use \Bean;

abstract class Sql extends Query {
	
	// получает общее записей
	public static function getCount(string $where = ''): int {
		return \R::count(self::getTable(), $where);
	}

	// получаем список всех записей
	public static function getAll(): array {
		return \R::findAll(self::getTable());
	}

	// получаем из БД запись по id
	public static function getById(int $id): ?Bean {
		return \R::load(self::getTable(), $id);
	}

}
