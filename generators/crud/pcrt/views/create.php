<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$formid = Inflector::camel2id(StringHelper::basename($generator->modelClass)). "_form";

echo "<?php\n";
?>

use yii\helpers\Html;

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words('Creazione ' . StringHelper::basename($generator->modelClass)))) ?>;

/* Bredcrumbs placeholder
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
*/

?>

<?= "<?php " ?>$this->beginBlock('actionButtons') ?>
    <?= "<?= " ?>Html::a('<i class="fas fa-plus"></i> Salva' , ['class' => 'btn btn-success', 'form' => '<?= $formid ?>' ]) ?>
<?= "<?php " ?>$this->endBlock() ?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
        'formname' => '<?= $formid ?>'
    ]) ?>

</div>
