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
               <?php if($icon):?>
                    <i class="fa fa-<?=$icon?> fa-lg pull-left"></i>
                <?php endif;?>  <?=$content;?>
    <?php endif;?>

</div>