<?php
use yii\web\JsExpression;

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
<div id="modal_" style="display:none;"></div>
$this->renderPartial('_form'['formname' => '<?= $formid ?>']);

Modal::end();

<?php 

$script = new JsExpression("
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
});");

echo "<script type=\"text/javascript\">";
echo $script;
echo "</script>";

?>