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

<div class="flatter">
    <div class="flatter-heading">
        <h4 class="flatter-title">
            <?php if($icon):?>
                <i class="fa fa-<?=$icon?> fa-lg"></i>
            <?php endif;?>
            <?=$title?>
        </h4>
    </div>
    <div class="flatter-body"><?=$content;?>
    </div>
    <div class="flatter-footer">

    </div>
</div>