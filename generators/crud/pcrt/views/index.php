<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace("Controller","",$controllerClass));
$type = ($generator->indexWidgetType === 'grid') ? "'Pagination'" : "'InfiniteScroll'";
echo "<?php\n";
?>

use yii\helpers\Html;
use pcrt\Paginator;
use yii\web\JsExpression;


/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

  <?= "<?php " ?>$this->beginBlock('actionButtons') ?>
      <?= "<?= " ?>Html::a('<i class="fas fa-plus"></i> Nuovo' ?>, ['create'], ['class' => 'btn btn-success']) ?>
  <?= "<?php " ?>$this->endBlock() ?>

<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>
<?php $append = ($generator->indexWidgetType !== 'grid') ? "'append'=>'.pcrt-card'," : ""; ?>
<?php echo "
    <?php Paginator::begin([
      'type' => ".$type.",
      'url' => 'index.php?r=".$controllerName."/list',
      " . $append . "
      'pageSize' => 30,
      'view' => \$this,
    ]) ?>

    <?php Paginator::end() ?>"; ?>


<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
</div>
