<?php

/**
 * @link http://www.protocollicreativi.it
 * @copyright Copyright (c) 2017 Protocolli Creativi s.n.c.
 * @license LICENSE.md
 */

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

/**
 * Yii2 Alternative code generator implementing more widget and functionality :
 * Automatic code generator for ajax pagination using pcrt/yii2-ajax-pager ( https://github.com/pcrt/yii2-ajax-pager )
 * Automatic date/datetime field recognize using pcrt/yii2-datepicker ( https://github.com/pcrt/yii2-datepicker )
 * Automatic foreignKeys detect and implement select2 field using pcrt/yii2-select2 ( https://github.com/pcrt/yii2-select2 )
 *
 * @author Marco Petrini <marco@bhima.eu>
 */

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


  /**
  * Generate code for Select2 field using pcrt/yii2-select2
  * @param string $table The name of origin Table
  * @param string $column The name of origin Column
  * @param string $fktable The name of lookup Table
  * @param boolean $isfilter True if the field is user as filter
  * @return string
  */
  private function generateSelect2ActiveField($table, $column, $fktable, $isfilter)
  {
    // TODO: Need Improve for CamelCase ControllerClass
    $controllerClass = StringHelper::basename($this->controllerClass);
    $controllerClass = lcfirst(str_replace("Controller","",$controllerClass));

    if(!$isfilter){
      return "\$form->field(\$model, '$column')->widget(
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
      return "\$form->field(\$model, '$column')->widget(
          Select2::class,
          [
              'items' => [],
              'options' =>['name' => 'filter__$column'],
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
    }
  }


  /**
  * Generate code for DaterangePicker field using pcrt/yii2-datepicker
  * @param string $table The name of origin Table
  * @param string $column The name of origin Column
  * @param string $type Not used
  * @param boolean $isfilter True if the field is user as filter
  * @return string
  * TODO: Move locale to template for code optimization
  * TODO: I18n Support
  */
  private function generateDateTimepickerActiveField($table, $column, $type, $isfilter)
  {
    $locale = '[
        "format"=>"DD/MM/YYYY",
        "separator" => " - ",
        "applyLabel" => "Applica",
        "cancelLabel" => "Cancella",
        "fromLabel" => "From",
        "toLabel" => "To",
        "customRangeLabel" => "Custom",
        "weekLabel" => "W",
        "daysOfWeek" => [
            "Dom",
            "Lun",
            "Mar",
            "Mer",
            "Gio",
            "Ven",
            "Sab"
        ],
        "monthNames" => [
            "Gennaio",
            "Febbraio",
            "Marzo",
            "Aprile",
            "Maggio",
            "Giugno",
            "Luglio",
            "Agosto",
            "Settembre",
            "Ottobre",
            "Novembre",
            "Dicembre"
        ],
        "firstDay" => 1
    ]';

    if(!$isfilter){
      return "\$form->field(\$model, '$column->name')->widget(
          Datepicker::class,
          [
              'clientOptions' => [
                'singleDatePicker' => true,
                'showDropdowns' => true,
                'locale' => ".$locale."

              ]
          ]
      );";
    }else{
      return "\$form->field(\$model, '$column->name')->widget(
          Datepicker::class,
          [
              'options' =>['name' => 'filter__$column->name'],
              'clientOptions' => [
                'showDropdowns' => true,
                'locale' => ".$locale."
              ]
          ]
      );";
    }
  }

  /**
  * Function to get FK array
  * @return array
  * TODO: Need Improve for multicolumn FK ???
  */
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

  /**
  * Function to get serch field array
  * @return array
  * TODO: Is used, Is needed ???
  */
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
    return $search;
  }

  /**
   * @inheritdoc
   */
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
          return $this->generateSelect2ActiveField($tableSchema,,$f['fk_table'],false);
        }
      }

      if ($column->type === 'date' || $column->type === 'datetime') {
          return $this->generateDateTimepickerActiveField($tableSchema,$column->name,'date',false);
      }

      return parent::generateActiveField($attribute);
  }

  /**
   * @inheritdoc
   */
  public function generateActiveSearchField($attribute)
  {
      $tableSchema = $this->getTableSchema();

      if ($tableSchema === false) {
          //return "Html::textInput('filter__$attribute', '', ['class' => 'form-control']);";
          return "\$form->field(\$model, '$attribute')->textInput(['name' => 'filter__$attribute', 'class' => 'form-control' ])";
      }

      $column = $tableSchema->columns[$attribute];

      $fk = $this->getForeignKeys();
      foreach($fk as $f){
        if($f['field']==$column->name){
          return $this->generateSelect2ActiveField($tableSchema,$column->name,$f['fk_table'],true);
        }
      }

      if ($column->phpType === 'boolean') {
          return "Html::checkbox('filter__$attribute', false, ['class' => 'form-control']);";
      }

      if ($column->type === 'date' || $column->type === 'datetime') {
          return $this->generateDateTimepickerActiveField($tableSchema,$column->name,'date',true);
      }

      //return "Html::textInput('filter__$attribute', '', ['class' => 'form-control']);";
      return "\$form->field(\$model, '$attribute')->textInput(['name' => 'filter__$attribute', 'class' => 'form-control' ])";
  }

}
