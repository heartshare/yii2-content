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

<div class="bs-callout bs-callout-<?=$type?>">

            <?php if($title):?>
    <h4>  <?=$title?></h4>
            <?php endif;?>


    <p><?php if($icon):?>
            <i class="fa fa-<?=$icon?> fa-2x pull-left"></i>
        <?php endif;?><?=$content;?>
    </p>
    <div class="clearfix"></div>
</div>