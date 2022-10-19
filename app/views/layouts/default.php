<?php
use ishop\Logger;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- получаем разметку с мета-тегами из вида -->
    <?=$this->getMeta();?>
</head>
<body>

<h1>Шаблон DEFAULT</h1>

<? //require_once __DIR__ . '/config/config.php'; ?>
<? //session_start(); ?>

<!-- динамичная часть контента - переданный вид -->

<?=$content;?>

<!-- выводим все запросы выполняемые RedBeanPHP -->
<?php Logger::getLog(); ?>
<?php
//$logs = \R::getDatabaseAdapter()
//    ->getDatabase()
//    ->getLogger();

//debug( $logs->grep( 'SELECT' ) );
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
?>
</body>
</html>
