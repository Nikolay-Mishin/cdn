<?php
use ishop\App;
use app\widgets\filter\Filter;
?>

<?=$breadcrumbs;?>

<!--prdt-starts-->
<div class="prdt">
    <div class="container">
        <div class="prdt-top">
            <div class="col-md-9 prdt-left">
                <!-- выводим товары -->
                <?php if (!empty($products)): ?>
                    <div class="product-one">
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
                                        <!-- цена товара -->
                                        <h4>
                                            <a data-id="<?=$product->id;?>" class="add-to-cart-link" href="cart/add?id=<?=$product->id;?>"><i></i></a>
                                            <!-- цена товара -->
                                            <span class=" item_price"><?=$curr['symbol_left'];?><?=price_format($product->price * $curr['value']);?><?=$curr['symbol_right'];?></span>
                                            <!-- выводим старую цену, если такая есть -->
                                            <?php if ($product->old_price): ?>
                                                <small><del><?=$curr['symbol_left'];?><?=price_format($product->old_price * $curr['value']);?><?=$curr['symbol_right'];?></del></small>
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
                    </div>
                <?php else: ?>
                    <h3>В этой категории товаров пока нет...</h3>
                <?php endif; ?>
            </div>

            <!-- фильтры -->
            <div class="col-md-3 prdt-right">
                <div class="w_sidebar">
                    <?php new Filter(); ?>
                    <!--<section  class="sky-form">
                        <h4>Catogories</h4>
                        <div class="row1 scroll-pane">
                            <div class="col col-4">
                                <label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i></i>All Accessories</label>
                            </div>
                            <div class="col col-4">
                                <label class="checkbox"><input type="checkbox" name="checkbox"><i></i>Women Watches</label>
                                <label class="checkbox"><input type="checkbox" name="checkbox"><i></i>Kids Watches</label>
                                <label class="checkbox"><input type="checkbox" name="checkbox"><i></i>Men Watches</label>
                            </div>
                        </div>
                    </section>-->
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<!--product-end-->
