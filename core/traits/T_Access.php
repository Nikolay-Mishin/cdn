<?php
// трейт для создания единственного экземляра класса (если он уже создан обращение идет к уже созданному экземпляру)
// трейт (trait) (кусок кода, который мы можем копипастить в другие файлы)
// реализаванный в трейте код вставляется в месте, где он задействован (подключен)

namespace ishop\traits;

trait T_Access {

    protected array $access = ['get' => [], 'set' => [], 'call' => []];

    public function __get(string $property): mixed {
        return $this->verifyAccess('get', $property);
    }

    public function __set(string $property, mixed $value): mixed {
        return $this->verifyAccess('set', $property, $value);
    }
  
    public function __call(string $method, array $args): mixed {
        return $this->verifyAccess('call', $method, $args);
    }

    public function verifyAccess(string $type, string $property, mixed $value = null): mixed {
        debug(['type' => $type, 'context' => getContext()]);
        $exist = property_exists($this, $property);
        $inGet = !$this->access['get'] ?: in_array($property, $this->access['get']);
        $inSet = !$this->access['set'] ?: in_array($property, $this->access['set']);
        if ($type == 'call' && !$this->access['call'] ?: in_array($property, $this->access['call'])) {
            return callPrivateMethod($this, $method, $args);
        }
        return $exist && ($inSet || $inGet) ? ($type != 'set' ? $this->$property : $this->$property = $value) : null;
    }

}
