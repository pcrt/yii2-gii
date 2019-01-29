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

        // Verifico se esiste la chiave relativa alla tabella
        if (isset($_SESSION[$tablename])){
          // Se la variabile name non esiste la inizializzo a null
          if ($name !== false && !isset($_SESSION[$tablename][$name])){
            return $_SESSION[$tablename][$name] = null;
          // Se name non è impostato restituisco tutta l'array dei filtri
          }elseif($name !== false){
            return $_SESSION[$tablename];
          }
          // Ritorno la chiave name
          $_SESSION[$tablename][$name] = null;
        }
        // Inizializzo la chiave relativa alla tabella e ritorno
        $_SESSION[$tablename] = null;
        return $_SESSION[$tablename];
    }

    public function setFilter($name, $value) {
        $tablename = $this->tablename;
        $_SESSION[$tablename][$name] = $value;
    }

    /*public static function initFilter() {
        $listA = ['filtro_data_amm', 'filtro_cliente_amm', 'filtro_vettore_amm', 'filtro_riferimento_amm', 'filtro_tragitto_amm', 'filtro_bolle_amm',
        'filtro_tipologia_amm', 'filtro_fatturazione_amm', 'filtro_itinerario_amm', 'filtro_creatoda_amm', 'filtro_stabilimento_amm', 'filtro_cdc_amm'];//amministrativo

        $listO = ['filtro_data', 'filtro_cliente', 'filtro_vettore', 'filtro_itinerario', 'filtro_archivio', 'filtro_riferimento', 'filtro_evase',
        'filtro_ordini_interni', 'filtro_tragitto_op', 'filtro_tragitto', 'filtro_tipologia', 'filtro_creatoda', 'filtro_stabilimento'];

        $listUC = ['filtro_data_cliente', 'filtro_riferimento_cliente', 'filtro_itinerario_cliente', 'filtro_stato_cliente', 'filtro_servizio_cliente'];

        $list = array_merge($listA, $listO, $listUC);

        $filters = self::getFilter();

        foreach ($list as $l) {
            if (!isset($filters[$l]))
                self::setFilter($l, null);
        }
    }*/
}
