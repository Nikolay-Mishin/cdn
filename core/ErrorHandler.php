<?php
// класс для отлова исключений
// не перехватывает ошибки (фатальные/ не фатальные (warning/notice)), только исключения (полный отлов в курсе по фреймворку)

namespace ishop;

use \Throwable; // Exception, Error implements Throwable

class ErrorHandler {

    public function __construct() {
        // уровень вывода ошибок устанавливаем в зависимости от значения констатнты дебага
        if (DEBUG) {
            error_reporting(-1);
        }
        else {
            error_reporting(0);
        }
        set_exception_handler([$this, 'exceptionHandler']); // назначаем свою функцию для обработки исключений
    }

    // метод для обработки исключений (курс по фреймворку)
    public function exceptionHandler(Throwable $e): void {
        // $e - оъект, содержащий всю информацию об выброшенном исключении
        // getMessage() - текст исключения
        // getFile() - файл, в котором было выброшено исключение
        // getLine() - строка с исключением
        // getCode() - код исключения
        $this->logErrors($e->getMessage(), $e->getFile(), $e->getLine());
        $this->displayError('Исключение', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    }

    // метод для логирования ошибок
    protected function logErrors(string $message = '', string $file = '', string $line = ''): void {
        // $message - текст ошибки
        // $file - файл, в котором произошла ошибка
        // $line - строка, в которой произошла ошибка
        // message_type = 3 - запись в файл (2 - отправка по email)
        error_log("[" . date('Y-m-d H:i:s') . "] Текст ошибки: {$message} | Файл: {$file} | Строка: {$line}\n=================\n", 3, TMP . '/errors.log');
    }

    // метод для вывода ошибкок и подключения шаблона
    protected function displayError(string $errno, string $errstr, string $errfile, int $errline, int $responce = 404): void {
        // $errno - код ошибки
        // $errstr - текст ошибки
        // $errfile - файл, в котором произошла ошибка
        // $errline - строка, в которой произошла ошибка
        // $responce - http код для отправки браузеру
        http_response_code($responce); // отправляем заголовок (код ответа для http заголовка)
        // показываем шаблон 404 ошибки, если код ответа 404 и константа дебаг = 0
        if ($responce == 404 && !DEBUG) {
            require WWW . "/errors/{$responce}.php";
            die;
        }
        // в режиме отладки показываем шаблон с полным описанием ошибки (шаблон разработчика)
        if (DEBUG) {
            require WWW . '/errors/dev.php';
        }
        // иначе показываем шаблон для продакшен версии с сообщением, что ошибка произошла (инфо об ошибке смотрим в логах)
        else {
            require WWW . '/errors/prod.php';
        }
        die;
    }

}
