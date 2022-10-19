<?php

set_time_limit(0);

$link = "";
$start = 1;
$count = 94;
$ext = "jpg";
$path = "D:\img";

?>

<div id="result">
    <?php for ($i = $start; $i <= $count; $i++): ?>
        <div id="result">Загружено: <?="$i/$count";?></div>
        <?php file_put_contents("$path/$i.$ext", file_get_contents("$link/$i.$ext")); ?>
        <img src=<?="$path/$i.$ext";?> alt="<?=$i;?>">
    <?php endfor; ?>
</div>