<?php
use ishop\App;
?>

<?php if (!empty($products)): ?>
    <!-- получаем активную валюту из контейнера -->
    <?php $curr = App::$app->getProperty('currency'); ?>
    <!-- выводим отдельно каждый товар -->
    <?php foreach ($products as $product): ?>
        <div class="col-md-4 product-left p-left">
            <div class="product-main simpleCart_shelfItem">
                <!-- изображение -->
                <a href="product/<?=$product->alias;?>" class="mask"><img class="img-responsive zoom-img" src="images/<?=$product->img;?>" alt="" /></a>
                <div class="product-bottom">
                    <!-- наименование -->
                    <h3><a href="product/<?=$product->alias;?>"><?=$product->title;?></a></h3>
                    <p>Explore Now</p>
                    <h4>
                        <!-- ссылка для добавления товара в корзину (текущая цена) -->
                        <a data-id="<?=$product->id;?>" class="add-to-cart-link" href="cart/add?id=<?=$product->id;?>"><i></i></a> <!-- цена товара -->
                        <span class=" item_price"><?=$curr['symbol_left'];?><?=$product->price * $curr['value'];?><?=$curr['symbol_right'];?></span>
                        <!-- выводим старую цену, если такая есть -->
                        <?php if ($product->old_price): ?>
                            <small><del><?=$curr['symbol_left'];?><?=$product->old_price * $curr['value'];?><?=$curr['symbol_right'];?></del></small>
                        <?php endif; ?>
                    </h4>
                </div>
                <!-- рассчет размера скидки (при наличии старой цены) -->
                <?php if ($product->old_price > 0): ?>
                    <div class="srch">
                        <span>-<?=number_round((1 - $product->price / $product->old_price) * 100);?>%</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- // выводим отдельно каждый товар -->
    <div class="clearfix"></div>
    <!-- пагинация -->
    <div class="text-center">
        <!-- выводим количество товаров из общего числа -->
        <p>(<?=count($products)?> товара(ов) из <?=$total;?>)</p>
        <!-- если у нас больше 1 страницы с товарами, выводим пагинацию -->
        <?php if ($pagination->countPages > 1): ?>
            <!-- объект пагинации преобразуется к строке благодаря магическому методу __toString() -->
            <?=$pagination;?>
        <?php endif; ?>
    </div>
    <!-- // пагинация -->
<?php else: ?>
    <h3>Товаров не найдено...</h3>
<?php endif; ?>
