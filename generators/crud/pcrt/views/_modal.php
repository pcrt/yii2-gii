<?php
use yii\web\JsExpression;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$formid = 'modal_'.Inflector::camel2id(StringHelper::basename($generator->modelClass));
$modaliderror = $formid."_error";
$controllerClass = StringHelper::basename($generator->controllerClass);
$controllerName = lcfirst(str_replace("Controller","",$controllerClass));

echo "<?php\n";
?>
use yii\bootstrap4\Modal;

Modal::begin([
     'title' => '<h2></h2>',
     'toggleButton' => ['label' => 'Chiudi'],
]);

<php echo "?>\n"; ?>

<div id="modal_" style="display:none;"></div>

<?php echo "<?php\n"; ?>

$this->renderPartial('_form'['model' => $model, 'formname' => '<?= $formid ?>']);

Modal::end();

<php echo "?>\n"; ?>

<?php 

$script = new JsExpression("
  document.addEventListener(\"DOMContentLoaded\", function(event) {
    $('#$formid').submit(function(e){
      e.preventDefault();
      $('#$formid').ajaxSubmit({
        url: 'index.php?r=$controllerName/create', 
        type: 'post',
        after: function(res) {
          $('#$modaliderror').hide();
        },
        success: function(res) {
          JSON.parse(res);
          if(res.code == 500){
              $('#$modaliderror').html(res.errors);
              $('#$modaliderror').show();
          }
        },
        error: function(res) {
          $('#$modaliderror').html();
          $('#$modaliderror').show();
        }
      });
    });
  });");

echo "<script type=\"text/javascript\">";
echo $script;
echo "</script>";

?>