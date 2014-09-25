<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 30.07.14
 * Time: 0:08
 */

namespace insolita\content\modules\cover\widgets;


use insolita\widgets\Dialog\Dialog;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class CoverChooser extends InputWidget
{

    /**
     * @var string $id - widget $id.
     */
    public $id;
    /**
     * @var int $cols - number of columns for image
     */
    public $cols = 3;
    /**
     * @var string $showto - selector, where we put selected image
     */
    public $showto = false;

    public $covtype = 'all';

    private $target_id;
    private $_tpl;

    public function init()
    {


    }

    public function run()
    {
        $this->renderInput();
        $this->registerAssets();
    }

    protected function renderInput()
    {
        $this->target_id = $this->id;
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'form-control input-sm';
        } else {
            $this->options['class'] .= ' form-control';
        }
        $this->options['id'] = $this->id;
        $this->renderModal();
        if ($this->hasModel()) {
            echo Html::a('...', '#', ['class' => 'btn btn-sm btn-default', 'id' => $this->target_id . 'btn']);
            echo Html::activeHiddenInput($this->model, $this->attribute, $this->options);

        } else {
            echo Html::hiddenInput($this->name, $this->value, $this->options);
        }
    }

    protected function renderModal()
    {
        Dialog::widget(
            [
                'selector' => '#' . $this->target_id . 'btn',
                'options' => ['id' => 'choosemodal'],
                'size' => 'large',
                'title' => 'Выбрать изображение',
                'content' => false,
                'remote' => Url::to(['/content/cover/default/index', 'type' => $this->covtype]),
                'onHideEvent' => new JsExpression('function(dialogRef){
                        $(document).off("click","a[data-pjaxtarget_cover]");
                        }
                ')
            ]
        );
    }


    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $delurl = Url::to(['/content/cover/default/delete']);
        $js[] = new JsExpression('$(document).on("click","[data-choose]",function(e) {
                        e.preventDefault();
                        $("#' . $this->target_id . '").val($(this).data("choose"));
                        ' . ($this->showto ? '$("#' . $this->showto . '").html($(this).html());' : '') . '
                        thisDialog.close();
                });');
        $js[] = new JsExpression('$(document).on("click","[data-remcover]",function(e) {
                        e.preventDefault();
                        var delid=$(this).data("remcover");
                        var selid=$("#' . $this->target_id . '").val();
                        if(delid==selid){
                           $("#' . $this->target_id . '").val("");
                            ' . ($this->showto ? '$("#' . $this->showto . '").html("");' : '') . '
                        }
                        $.post("' . $delurl . '",{id:$(this).data("remcover")},function(data){
                             $.pjax.reload({container:"#coverpjax",timeout:5000,url:"'
            . Url::to(['/content/cover/default/list', 'type' => $this->covtype])
            . '",push:false,replace:false,scrollTo:"#coverpjax"});
                        });
                });');
        $js[] = new JsExpression('$(document).on("click","#remchoose",function(e) {
                        e.preventDefault();
                        $("#' . $this->target_id . '").val("");
                        ' . ($this->showto ? '$("#' . $this->showto . '").html("");' : '') . '
                        thisDialog.close();
                });');
        $view = $this->getView();
        $view->registerJs(implode("", $js),View::POS_READY , 'imgchooser' . $this->id);
    }

} 