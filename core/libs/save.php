<?php
require_once "Parser.php";

function show($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function save($link, $path) {
    file_put_contents($path, file_get_contents($link));
}

//save("https://sun9-80.userapi.com/impf/c846524/v846524191/123f16/S3K1YuPRSy4.jpg?size=604x329&quality=96&sign=ee6c79f492b7dd05c963f3b1c08bd237&type=album", "D:\\YandexDisk\\_!Арты\\!_!vk_арты\\!_New_2\\hq8ph4-e5GY.jpg");

//save("https://vk.com/photo174508998_456245234", "D:\\YandexDisk\\_!Арты\\!_!vk_арты\\!_New_2\\save.html");

// show($_GET);

// echo file_get_contents("https://vk.com/photo174508998_456245234");

// echo file_get_contents("https://vk.com/album174508998_257883883");

$html = "";

switch ($_GET["id"] ?? 0) {
    case 1:
        $html = Parser::getPage([
            "url" => "http://httpbin.org/ip"
        ]);
        break;

    case 2:
        $html = Parser::getPage([
            "url" => "http://yandex.ru/"
        ]);
        break;

    case 3:
        $html = Parser::getPage([
            "url" => "https://vk.com/photo174508998_456245234"
        ]);
        break;

    case 4:
        $html = Parser::getPage([
            "url" => "https://vk.com/album174508998_257883883"
        ]);
        break;

    default:
        break;
}

// show($html);

echo $html["data"]["content"];
