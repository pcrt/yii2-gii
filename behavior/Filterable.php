<?php

namespace pcrt\behavior;

use Yii;
use yii\base\Behavior;
use yii\web\Session;

class Filterable extends Behavior
{
    public $tablename = "";

    public function getFilter($name = false) { //senza nome restituisco tutti i filtri
        $tablename = $this->tablename;
        $session = Yii::$app->session;
        // Verifico che il parametro name non sia nullo
        if ($name !== false){
          return $session->get($tablename.".".$name, null);
        // Se name non è impostato restituisco tutta l'array dei filtri
        }elseif($name !== false){
          $_session = [];
          foreach ($session as $name => $value){
            $_session[$name] = $value;
          }
          return $_session;
        }
        return;
    }

    public function setFilter($name, $value) {
        $tablename = $this->tablename;
        $session = Yii::$app->session;
        $session->set($tablename.".".$name, $value); 
        return;
    }
}
