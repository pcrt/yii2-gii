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
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<h1><?= "<?= " ?>Html::encode($this->title) ?></h1>

<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>



<?php echo "
    <?php Paginator::begin([
      'type' => ".$type.",
      'url' => 'index.php?r=".$controllerName."/list',
      'pageSize' => 30,
      'view' => \$this,
    ]) ?>

    <?php Paginator::end() ?>"; ?>


<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
</div>
