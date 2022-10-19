<?php
// базовый класс модели фреймворка - описывает базовые свойства и методы, которые будут наследоваться всеми моделями
// необходим для работы с БД

namespace ishop\base;

use \Bean;

use ishop\Db; // класс БД
use ishop\base\db\Sql;

use Valitron\Validator; // класс Валидатора

abstract class Model extends Sql {

	use \ishop\traits\T_SetProperties;
	use \ishop\traits\T_Protect;
	use \ishop\traits\T_GetContents;

	public array $attributes = []; // массив свойств модели (идентичен полям в таблицах БД - автозагрузка данных из форм в модель)
	public array $errors = []; // хранение ошибок
	public array $rules = []; // правила валидации данных
	public ?int $id = null; // id последней сохраненной записи (метод save())
	protected Bean $bean;

	public function __construct(array|string|int $data = [], array|string|int $attrs = [], string $action = 'save', array|string $valid = []) {
		if (CUSTOM_DB_INSTANCE) Db::instance(); // создаем объект класса БД

		$this->addProtectProperties('bean => set');
		$this->bean = \R::load(self::getTableName(), 1); // получаем бин записи из БД (структуру объекта)
		//debug(['ProtectProperties' => $this->getProtectProperties()]);
		//debug(['bean' => $this->bean]);

		// если в конструктор модели переданы данные, то загружаем их в свойство $attributes модели и сохраняем в БД
		if ($data) {
			list($data, $attrs, $valid) = toArray([$data, $attrs, $valid], true);
			$this->load($data); // загружаем полученные данные в модель
			// валидируем данные
			// in_array(,,true) - при поиске будет использовано строгое сравнение (проверит соответствие типов)
			if (!$this->validate($data) || in_array(false, validateAttrs($this, $valid), true)) {
				$this->getErrors(); // получаем список ошибок
				if ($action == 'save') $_SESSION['form_data'] = $data;
				redirect();
			}
			$this->table = self::getTable(); // имя таблицы в БД
			// сохраняем/обновляем данные в БД и получаем id последней сохраненной записи
			// Вызывает callback-функцию `callback`, с параметрами из массива `param_arr`
			//$this->$action($attrs);
			//call_user_func_array([$this, $action], $attrs);
			$this->$action(...$attrs);
		}
	}

	// метод автозагрузки данных из формы
	// защищает от получения данных, которых нет в форме (будут заполнены только данные, которые есть в аттрибутах модели)
	/*
	'login' => '',
	'password' => '',
	// данного поля нет в свойстве $attributes
	'login1' => '', // поле, которого не было в форме (если со стороны клиенты, например будет попытка подмены формы)
	*/
	protected function load(array $data): void {
		foreach ($this->attributes as $name => $value) {
			// если в данных есть поле, соответствующее полю в $attributes, то записываем значение из данных в аттрибуты
			if (isset($data[$name])) {
				$this->attributes[$name] = $data[$name];
			}
		}
	}

	// сохраняем данные в таблицу в БД
	protected function save(string $table = '', bool $valid = true): Bean {
		// если имя таблицы валидно, используем метод dispense, иначе xdispense
		// '_' в имени запрещено для RedBeanPHP => attributeValue вместо attribute_value)
		// производим 1 из операций CRUD - Create Update Delete
		// создаем бин (bean) - новую строку записи для сохранения данных в таблицу в БД
		//if ($valid) {
		//    $tbl = \R::dispense($table);
		//}
		//else {
		//    $tbl = \R::xdispense($table);
		//}
		$tbl = !preg_match('/_/', $this->table) ? \R::dispense($this->table) : \R::xdispense($this->table);
		//// в каждое поле таблицы записываем соответствуещее значение из списка аттрибутов модели
		//foreach ($this->attributes as $name => $value) {
		//    $tbl->$name = $value;
		//}
		//// сохраняем сформированные данные в БД и возвращаем результат сохранения (id записи либо 0)
		//return $this->id = \R::store($this->bean = $tbl);
		return $this->saveBean($tbl);
	}

	// метод обновления (перезаписи) данных в БД
	protected function update(int $id, string $table = ''): Bean {
		//$bean = \R::load($table, $id);
		$bean = \R::load($this->table, $id); // получаем бин записи из БД (структуру объекта)
		//// для каждого аттрибута модели заполняем поля записи в БД
		//foreach ($this->attributes as $name => $value) {
		//    $bean->$name = $value;
		//}
		//// сохраняем сформированные данные в БД и возвращаем результат сохранения (id записи либо 0)
		//return $this->id = \R::store($this->bean = $bean);
		return $this->saveBean($bean);
	}

	// метод сохранения бина (представления таблицы) в БД
	protected function saveBean(Bean $bean): Bean {
		// для каждого аттрибута модели заполняем поля записи в БД
		foreach ($this->attributes as $name => $value) {
			$bean->$name = $value;
		}
		$this->id = \R::store($this->bean = $bean);
		// сохраняем сформированные данные в БД и возвращаем результат сохранения (id записи либо 0)
		return $this->bean;
	}

	// метод валидации данных
	protected function validate(array $data): bool {
		Validator::langDir(WWW . '/validator/lang'); // указываем Валидатору директорию для языков
		Validator::lang('ru'); // устанавливаем язык Валидатора
		$v = new Validator($data); // объект Валидатора (передаем данные в конструктор)
		$v->rules($this->rules); // передаем в Валидатор набор правил валидации
		// если валидация прошла успешно, возвращаем true
		if ($v->validate()) {
			return true;
		}
		$this->errors = $v->errors(); // записываем ошибки валидации в свойство модели
		return false;
	}

	// получает ошибки валидации
	public function getErrors(): void {
		// формируем список ошибок
		$errors = '<ul>';
		foreach ($this->errors as $error) {
			foreach ($error as $item) {
				$errors .= "<li>$item</li>";
			}
		}
		$errors .= '</ul>';
		$_SESSION['error'] = $errors; // записываем список ошибок в сессию
	}

	public function setRequired(array $data, string ...$required): void {
		$this->rules['required'] = [];
		$this->addRequired($data, ...$required);
	}

	protected function addRequired(array $data, string ...$required): void {
		$data = arrayGetValues($data, $required);
		foreach ($data as $k => $v) {
			$this->rules['required'][] = toArray($k);
		}
	}

}
