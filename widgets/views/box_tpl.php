<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 25.09.14
 * Time: 23:40
 *
 *
 * @var string $type
 * @var string $icon
 * @var string $title
 * @var string $content
 */
?>
<div class="box box-<?=$type?>">
    <div class="box-header">
        <?php if($icon):?>
            <i class="fa fa-<?=$icon?> fa-lg"></i>
        <?php endif;?>
        <h3 class="box-title"><?=$title?></h3>
    </div>
    <div class="box-body"><?=$content;?>
    </div>
</div>