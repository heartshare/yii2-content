<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 25.09.14
 * Time: 23:40

 * @var string $type
 * @var string $icon
 * @var string $title
 * @var string $content
 */
?>

<div class="panel panel-<?=$type?>">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php if($icon):?>
                <i class="fa fa-<?=$icon?> fa-lg"></i>
            <?php endif;?>
            <?=$title?></h3>
    </div>
    <div class="panel-body"><?=$content;?>
    </div>
</div>