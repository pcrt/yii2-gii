<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$foreignKeys = $generator->getForeignKeys();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace("Controller","",$controllerClass));

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
<?php if(count($foreignKeys) !== 0) : ?>
use pcrt\widgets\select2\Select2;

<?php endif; ?>
use pcrt\widgets\datepicker\Datepicker;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(
          [
            // TODO : Setting the form property
            'layout' => 'horizontal',
            'id' => <?php echo "(isset(\$formname)) ? \$formname : '" . Inflector::camel2id(StringHelper::basename($generator->modelClass)) . "' . rand ()" ?>,
            /*'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-4',
                    'offset' => 'offset-sm-4',
                    'wrapper' => 'col-sm-8',
                    'error' => '',
                    'hint' => '',
                ],
            ],*/
          ]
    ); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?php " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    <?php echo "<?php if( isset(\$hidebutton) === false ): ?>" ?>
    <div class="form-group">
        <?= "<a href=\"?r=$controllerName\" class='btn btn-secondary'><i class='fas fa-times'></i> Cancella</a>"; ?> <?= "\n" ?>
        <?= "<?= " ?>Html::submitButton('<i class="fas fa-plus"></i> Salva', ['class' => 'btn btn-success']) ?>
    </div>
    <?php echo "<?php endif; ?>" ?>
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
