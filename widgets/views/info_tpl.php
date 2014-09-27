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
    <h4 class="text-<?=type?>"> <?=$title?></h4>
            <?php endif;?>

    <?php if($content):?>
        <div class="row">
            <div class="col-sm-1 col-md-1"><?php if($icon):?>
                    <i class="fa fa-<?=$icon?> fa-2x"></i>
                <?php endif;?> </div>
            <div class="col-sm-11 col-md-11">
       <?=$content;?>
        </div></div>
    <?php endif;?>

</div>