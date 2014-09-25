<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 18.08.14
 * Time: 2:04
 *
 * @var yii\web\View                        $this
 * @var yii\data\ActiveDataProvider         $dp
 * @var  insolita\content\search\NewsSearch $model
 */
?>

<div class="box box-primary art-item">
    <div class="box-header">
        &nbsp;&nbsp;<h3 class="box-title"><?=$model->name?></h3>
    </div>
    <div class="box-body">
            <label class="label label-primary"><?= date("d.m.Y", strtotime($model->created)); ?></label>
            <?= $model->text ?>
    </div>
</div><!-- /.box-body -->
