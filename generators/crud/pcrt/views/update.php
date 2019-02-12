<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$modelClassName = Inflector::camel2words(StringHelper::basename($generator->modelClass));
$nameAttributeTemplate = '$model->' . $generator->getNameAttribute();
$titleTemplate = $generator->generateString('Update ' . $modelClassName . ': {name}', ['name' => '{nameAttribute}']);
$formid = Inflector::camel2id(StringHelper::basename($generator->modelClass)). "_form";

$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace("Controller","",$controllerClass));

if ($generator->enableI18N) {
    $title = strtr($titleTemplate, ['\'{nameAttribute}\'' => $nameAttributeTemplate]);
} else {
    $title = strtr($titleTemplate, ['{nameAttribute}\'' => '\' . ' . $nameAttributeTemplate]);
}

echo "<?php\n";
?>

use yii\helpers\Html;

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words('Modifica '. StringHelper::basename($generator->modelClass)))) ?>;

/* Bredcrumbs placeholder
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Update') ?>;
*/

?>

<?= "<?php " ?>$this->beginBlock('actionButtons') ?>
<?= "<?= " ?>Html::SubmitButton('<i class="fas fa-plus"></i> Salva' , ['class' => 'btn btn-success', 'form' => '<?= $formid ?>' ]) ?> <?= "\n" ?>
<?= "<a href=\"?r=$controllerName\" class='btn btn-secondary'><i class='fas fa-times'></i> Cancella</a>"; ?> <?= "\n" ?>
<?= "<?php " ?>$this->endBlock() ?>


<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">

    <?= '<?= ' ?>$this->render('_form', [
        'model' => $model,
        'formname' => '<?= $formid ?>'
    ]) ?>

</div>
