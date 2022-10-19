<?php
// трейт для создания единственного экземляра класса (если он уже создан обращение идет к уже созданному экземпляру)
// трейт (trait) (кусок кода, который мы можем копипастить в другие файлы)
// реализаванный в трейте код вставляется в месте, где он задействован (подключен)

namespace ishop\traits;

trait T_Ajax {

    protected bool $isAjax = false;
    private ?string $request;

    // определяет, каким видом пришел запрос (асинхронно/ajax или нет)
    public function isAjax(): bool {
        $this->request = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
        return $this->isAjax = $this->request === 'XMLHttpRequest';
    }

}
