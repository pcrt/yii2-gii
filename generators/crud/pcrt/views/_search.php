<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace('Controller', '', $controllerClass));
echo "<?php\n";
?>
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use pcrt\widgets\select2\Select2;
use yii\web\JsExpression;
use pcrt\widgets\datepicker\Datepicker;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">
    <?= '<?php ' ?>$form = ActiveForm::begin([
        'method' => 'POST',
        'id' => 'filter_table'
        'action' => [''],
<?php if ($generator->enablePjax): ?>
        'options' => [
            'data-pjax' => 1
        ],
<?php endif; ?>
    ]); ?>
<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo '    <?php echo' . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    } else {
        echo '    <?php /* echo ' . $generator->generateActiveSearchField($attribute) . " */?>\n\n";
    }
}
?>
    <div class="form-group">
        <?= '<?= ' ?>Html::Button(<?= $generator->generateString('Apply') ?>, ['class' => 'btn btn-primary', 'id'=>'apply_filter']) ?>
        <?= '<?= ' ?>Html::Button(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default', 'id'=>'reset_filter']) ?>
    </div>
    <?= '<?php ' ?>ActiveForm::end(); ?>
</div>
<script type="text/javascript">
  var apply_filter = document.getElementById('apply_filter');
  apply_filter.addEventListener('click', function() {
    var filter_table = $('#filter_table');
    var data = filter_table.serialize();
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'index.php?r=<?=$controllerName?>/set-filter', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
      if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
        // TODO: Need Function to reload content widget

        window.reload_table();

      }
    }
    xhr.send(data);
  }, false);
</script>
