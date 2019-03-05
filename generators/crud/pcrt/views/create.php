<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
$formid = Inflector::camel2id(StringHelper::basename($generator->modelClass)) . '_form';
$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace("Controller", '', $controllerClass));
echo '<?php\n';
?>
use yii\helpers\Html;
$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words('Creazione ' . StringHelper::basename($generator->modelClass)))) ?>;
/* Bredcrumbs placeholder
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
*/
?>
<?= '<?php ' ?>$this->beginBlock('actionButtons') ?>
    <?= "<a href=\"?r=$controllerName\" class='btn btn-secondary'><i class='fas fa-times'></i> Cancella</a>"; ?> <?= "\n" ?>
    <?= '<?= ' ?>Html::SubmitButton('<i class="fas fa-plus"></i> Salva' , ['class' => 'btn btn-success', 'form' => '<?= $formid ?>' ]) ?> <?= "\n" ?>
<?= '<?php ' ?>$this->endBlock() ?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <?= '<?= ' ?>$this->render('_form', [
        'model' => $model,
        'formname' => '<?= $formid ?>'
    ]) ?>
</div>
