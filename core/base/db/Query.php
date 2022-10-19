<?php

namespace ishop\base\db;

abstract class Query {

	use \ishop\traits\T_Instance;

	protected ?string $table = null;

	protected static string $query = '';

	protected string $from = '';
	protected string $select = '';
	protected string $join = '';
	protected string $where = '';
	protected string $group = '';
	protected string $sort = '';
	protected string $order = '';
	protected string $limit = '';

	protected string $update = '';
	protected string $set = '';

	protected string $insert = '';
	protected string $into = '';
	protected string $values = '';

	protected string $delete = '';

	protected static function init(): static {
		return static::instance();
	}

	protected static function getTable(): string {
		return self::init()->table ??= self::getTableName();
	}

	// возвращает имя таблицы в БД на основе имени модели (thisMethodName => this_method_name)
	public static function getTableName(): string {
		return lowerCamelCase(getClassName(static::class));
	}

	protected static function getSql(): string {
		self::init();
		self::$query = '';
		if (self::init()->select) {
			self::init()->select()->from()->join()->where()->group()->order()->limit();
		}
		elseif (self::init()->set) {
			self::init()->update_sql()->set()->where();
		}
		elseif (self::init()->into && self::init()->values) {
			self::init()->insert()->into()->values();
		}
		elseif (self::init()->delete) {
			self::init()->delete_sql()->where();
		}
		return self::$query;
	}

	protected function select(string $select = ''): self {
		self::$query .= $this->select = "SELECT " . ($select ?: $this->select);
		return $this;
	}

	protected function from(string $from = ''): self {
		self::$query .= $this->from = PHP_EOL . "FROM `" . ($from ?: ($this->from ?: self::getTable())) . '`';
		return $this;
	}

	protected function join(string $join = ''): self {
		$join = explode(', ', $join ?: $this->join);
		$sql_part = '';
		foreach ($join as $joinItem) {
			$sql_part .= $joinItem ? "JOIN $joinItem" . PHP_EOL : '';
		}
		self::$query .= $this->join = rtrim($sql_part ? PHP_EOL . $sql_part : '', PHP_EOL);
		return $this;
	}

	protected function where(string $where = ''): self {
		self::$query .= $this->where = $where || $this->where ? PHP_EOL . "WHERE " . ($where ?: $this->where) : $this->where;
		return $this;
	}

	protected function group(string $group = ''): self {
		self::$query .= $this->group = $group || $this->group ? PHP_EOL . "GROUP BY " . ($group ?: $this->group) : $this->group;
		return $this;
	}

	protected function order(string $order = '', string $sort = ''): self {
		$order = explode(', ', $order ?: $this->order);
		$sql_part = '';
		foreach ($order as $orderItem) {
			$sql_part .= $orderItem ? "$orderItem " . $this->sort . ', ' : '';
		}
		self::$query .= $this->order = rtrim($sql_part ? PHP_EOL . "ORDER BY $sql_part" : $this->order, ', ');
		return $this;
	}

	protected function limit(string $limit = ''): self {
		self::$query .= $this->limit = $limit || $this->limit ? PHP_EOL . "LIMIT " . ($limit ?: $this->limit) : $this->limit;
		return $this;
	}

	protected function update_sql(string $update = ''): self {
		self::$query .= $this->update = "UPDATE `" . ($update ?: ($this->update ?: self::getTable())) . '`';
		return $this;
	}

	protected function set(string $set = ''): self {
		self::$query .= $this->set = PHP_EOL . "SET " . ($set ?: $this->set);
		return $this;
	}

	protected function insert(string $insert = ''): self {
		self::$query .= $this->insert = "INSERT INTO `" . ($insert ?: ($this->insert ?: self::getTable())) . '`';
		return $this;
	}

	protected function into(string $into = ''): self {
		self::$query .= $this->into = PHP_EOL . "(" . ($into ?: $this->into) . ")";
		return $this;
	}

	protected function values(string $values = ''): self {
		self::$query .= $this->values = PHP_EOL . "VALUES " . ($values ?: $this->values);
		return $this;
	}

	protected function delete_sql(string $delete = ''): self {
		self::$query .= $this->delete = "DELETE FROM `" . ($delete ?: ($this->delete ?: self::getTable())) . '`';
		return $this;
	}

}
