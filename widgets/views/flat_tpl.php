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

<?php if($icon || $title):?>
        <h4>
            <?php if($icon):?>
                <i class="fa fa-<?=$icon?> fa-lg"></i>
            <?php endif;?>
            <?=$title?>
        </h4>
<?php endif;?>
<?=$content;?>
