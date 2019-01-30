<?php


namespace pcrt\generators\crud;
use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\helpers\StringHelper;



class Generator extends \yii\gii\generators\crud\Generator
{
  public function getName()
  {
      return 'PCRT CRUD Generator';
  }
  /**
   * {@inheritdoc}
   */
  public function getDescription()
  {
      return 'This generator generates an ActiveRecord class for the specified database table with PCRT customization.';
  }

  private function generateSelect2ActiveField($table, $column, $fktable, $isfilter)
  {
    // TODO: Need Improve for CamelCase ControllerClass
    $controllerClass = StringHelper::basename($this->controllerClass);
    $controllerClass = lcfirst(str_replace("Controller","",$controllerClass));
    
    if(!$isfilter){
      return "\$form->field(\$model, '$column->name')->widget(
          Select2::class,
          [
              'items' => [],
              'clientOptions' => [
                'ajax' => [
                  'url' => 'index.php?r=$controllerClass/get-$fktable',
                  'delay' => '250',
                  'type' => 'POST',
                  'dataType' => 'json',
                  'data' => new JsExpression('function (params) {
                      return {
                        query: params.term || \"\",
                        page: params.page || 0,
                        _csrf : yii.getCsrfToken()
                      }
                  }')
                ],
                'cache' => 'false'
              ]
          ]
      );";
    }else{
      return "Select2::widget([
        'name' => 'filter__$column->name',
        'clientOptions' => [
          'ajax' => [
            'url' => 'index.php?r=$controllerClass/get-$fktable',
            'delay' => '250',
            'type' => 'POST',
            'dataType' => 'json',
            'data' => new JsExpression('function (params) {
                return {
                  query: params.term || \"\",
                  page: params.page || 0,
                  _csrf : yii.getCsrfToken()
                }
            }')
          ],
          'cache' => 'false'
        ]
      ]);";
    }
  }

  // TODO: Need Improve for multicolumn FK ???
  public function getForeignKeys(){
    $FK = [];
    $tableSchema = $this->getTableSchema();
    if ($tableSchema !== false) {
      $fk = $tableSchema->foreignKeys;
      foreach($fk as $f){
        $fk = [];
        $fk['table'] = $tableSchema->name;
        foreach ($f as $key => $val) {
          if($key === 0){
            $fk['fk_table'] = $val;
          }else{
            $fk['fk_field'] = $val;
            $fk['field'] = $key;
          }
        }
        $FK[] = $fk;
      }
    }
    return $FK;
  }

  public function getSearchField(){

    $search=[];
    $tableSchema = $this->getTableSchema();
    if ($tableSchema !== false){

      $columns = $tableSchema->columns;
      $fkey = $this->getForeignKeys();

      // TODO: Need Improvment for Many to Many Relation
      // Traverse every column and setting Type and FK
      foreach($columns as $c){

        $search[$c->name]['type']=$c->type;
        // If Type is Integer find for possible FK
        if($c->type === "integer"){
          // Setting default FK to empty array
          $search[$c->name]['fk'] = [];
          foreach($fkey as $f){
            if($f['field'] === $c->name){
              // If match setting FK value
              $search[$c->name]['fk'] = $f;
            }
          }
        }
      }
    }
    //\Yii::trace($search);
    return $search;
  }

  public function generateActiveField($attribute)
  {
      $tableSchema = $this->getTableSchema();
      
      if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
          if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
              return "\$form->field(\$model, '$attribute')->passwordInput()";
          }
          return "\$form->field(\$model, '$attribute')";
      }

      $column = $tableSchema->columns[$attribute];
      $fk = $this->getForeignKeys();
      
      foreach($fk as $f){
        if($f['field']==$column->name){
          return $this->generateSelect2ActiveField($tableSchema,$column,$f['fk_table'],false);
        }
      }

      return parent::generateActiveField($attribute);
  }

  public function generateActiveSearchField($attribute)
  {
      $tableSchema = $this->getTableSchema();
      
      if ($tableSchema === false) {
          return "Html::textInput('filter__$attribute', '', options[]);";
      }
      
      $column = $tableSchema->columns[$attribute];
      
      $fk = $this->getForeignKeys();
      foreach($fk as $f){
        if($f['field']==$column->name){
          return $this->generateSelect2ActiveField($tableSchema,$column,$f['fk_table'],true);
        }
      }
      
      if ($column->phpType === 'boolean') {
          return "Html::checkbox('filter__$attribute', false, options[]);";
      }
      
      return "Html::textInput('filter__$attribute', '', options[]);";
  }
  
}
