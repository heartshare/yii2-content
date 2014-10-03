<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:17
 */

namespace insolita\content\widgets;


use insolita\content\models\Article;
use insolita\content\models\News;
use yii\bootstrap\Collapse;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;

class ArchiveWidget extends TextWidget
{
    const SEARCH_NEWS = 'news';
    const SEARCH_ARTS = 'arts';

    const DEPTH_DAYS=0;
    const DEPTH_MONTH=1;

    /**@var string $searchtype Объект поиска - новости\Статьи*/
    public $searchtype = self::SEARCH_NEWS;

    /**@var string $depth Глубина вывода - по дням, по месяцам*/
    public $depth = self::DEPTH_MONTH;

    public function run()
    {
        $this->text = $this->renderText();
        return parent::run();
    }

    public function renderText()
    {
       if ($this->searchtype == self::SEARCH_NEWS) {
            return $this->getNews();
        } else {
            return $this->getArts();
        }
    }

    public function getNews()
    {
        $news =($this->depth==self::DEPTH_DAYS)? $data=News::find()->select(['id','name','publishto','COUNT(*) as cnt'])->published()
            ->groupBy([new Expression('DATE(publishto)')])->orderBy(['publishto'=>SORT_DESC])->asArray()->all()
            :News::find()->select(['id','name','publishto','COUNT(*) as cnt'])->published()
                ->groupBy([new Expression('MONTH(publishto)')])->orderBy(['publishto'=>SORT_DESC])->asArray()->all();
        return $this->renderNews($news);
    }

    public function getArts()
    {
        $arts =($this->depth==self::DEPTH_DAYS)?
            Article::find()->select(['id','name','publishto','COUNT(*) as cnt'])->published()->groupBy([new Expression('DATE(publishto)')])->orderBy(['publishto'=>SORT_DESC])->asArray()->all()
            :Article::find()->select(['id','name','publishto','COUNT(*) as cnt'])->published()->groupBy([new Expression('MONTH(publishto)')])->orderBy(['publishto'=>SORT_DESC])->asArray()->all();
        return $this->renderArts($arts);
    }

    public function renderNews($data){
        $levels=$items=[];
        foreach ($data as $news) {
             if($this->depth==self::DEPTH_MONTH){
                 $y=date('Y',strtotime($news['publishto'])).' г';
                 $ym=date('Y-m',strtotime($news['publishto']));
                 $levels[$y][]=Html::tag('li',
                     Html::a(
                         \Yii::$app->formatter->asDate(strtotime($news['publishto']),'LLLL').'&nbsp;'.Html::tag('span',$news['cnt'],['class'=>'badge']),
                         \Yii::$app->params['siteurl'].Url::toRoute(['/content/front/news-bydate','date'=>$ym])
                 ));
             }else{
                 $y=date('Y',strtotime($news['publishto']));
                 $ym=\Yii::$app->formatter->asDate(strtotime($news['publishto']),'LLLL').' '.$y;
                 $ymd=date('Y-m-d',strtotime($news['publishto']));
                 $levels[$ym][]=Html::tag('li',
                     Html::a(
                         date('d.m',strtotime($news['publishto'])).'&nbsp;'.Html::tag('span',$news['cnt'],['class'=>'badge']),
                         \Yii::$app->params['siteurl'].Url::toRoute(['/content/front/news-bydate','date'=>$ymd])
                     ),[]);
             }
        }

        foreach($levels as $l=>$cont){
           $items[$l]=[
               'content'=>Html::tag('ul',implode('',$cont),['class'=>'nav nav-stacked'])
           ];
        }
        return Collapse::widget(['items'=>$items]);
    }
    public function renderArts($data){
        $levels=$items=[];
        foreach ($data as $news) {
            if($this->depth==self::DEPTH_MONTH){
                $y=date('Y',strtotime($news['publishto'])).' г';
                $ym=date('Y-m',strtotime($news['publishto']));
                $levels[$y][]=Html::tag('li',
                    Html::a(
                        \Yii::$app->formatter->asDate(strtotime($news['publishto']),'LLLL').'&nbsp;'.Html::tag('span',$news['cnt'],['class'=>'badge']),
                        \Yii::$app->params['siteurl'].Url::toRoute(['/content/front/articles-bydate','date'=>$ym])
                    ));
            }else{
                $y=date('Y',strtotime($news['publishto']));
                $ym=\Yii::$app->formatter->asDate(strtotime($news['publishto']),'LLLL').' '.$y;
                $ymd=date('Y-m-d',strtotime($news['publishto']));
                $levels[$ym][]=Html::tag('li',
                    Html::a(
                        date('d.m',strtotime($news['publishto'])).'&nbsp;'.Html::tag('span',$news['cnt'],['class'=>'badge']),
                        \Yii::$app->params['siteurl'].Url::toRoute(['/content/front/articles-bydate','date'=>$ymd])
                    ));
            }
        }

        foreach($levels as $l=>$cont){
            $items[$l]=[
                'label'=>$l,
                'content'=>Html::tag('ul',implode('',$cont),['class'=>'nav nav-stacked'])
            ];
        }
        return Collapse::widget(['items'=>$items]);
    }


    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Архив записей';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/archive-widget-configure';
    }

    /**
     * @method getIsScript()
     * @return bool - if widget for script places, return true, if for content - return false
     *
     */
    public function getIsScript()
    {
        return false;
    }

    public function allowCache()
    {
        return true;
    }
}