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

// Include the modal in the view you need like this :
// echo $this->render('_modal');
// After you can call the modal to create new record like this :
// showModal() ;
// Or you can pass id if need update existing record like this :
// showModal(77);


$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css');

<?php echo "?>\n"; ?>

<?php

$script = new JsExpression("

    function showModal(id){
      if(id == undefined){
        id = '';
        var action = '/index.php?r=$controllerName/create';
      }else{
        var action = '/index.php?r=$controllerName/update&id='+id;
      }
      $.confirm({
        content:'url:/index.php?r=$controllerName/ajax-form&id='+id,
        columnClass: 'xlarge',
        buttons: {
            chiudi: function () {
            },
            salva: {
                btnClass: 'btn-success',
                action:function() {

                    /* Yii2 validation have bug that cause submit after validation
                    // https://github.com/yiisoft/yii2/issues/13105
                    // We have to find solution to validate input until fixed
                    //$('#$formid').data('yiiActiveForm').submitting = true;
                    //if($('#$formid').yiiActiveForm('validate')){*/
                        $.ajax({
                            method: 'POST',
                            cache: false,
                            url: action,
                            data: $('#$formid').serialize(),
                            beforeSend: function( xhr ) {
                            },
                            statusCode: {
                                500: function() {
                                }
                            }
                        }).done(function(data) {
                            // reload page
                            if(data.code == 200){
                              window.reload_table();
                              return false;
                            }else{

                            }
                        });
                    /*}else{
                    //    return false;
                    //}*/
                },
            }
        },
      });
    }"
);

echo "<script type=\"text/javascript\">";
echo $script;
echo "</script>";

?>

<!-- TODO: Need a css refactor ( fix for z-index: 9999999 ) -->
<style>
  .jconfirm{
    z-index: 100!important;
  }
</style>
