<?php

namespace pcrt\behavior;

use Yii;
use yii\base\Behavior;

// Add method to extract key,description array valid to Select2 dropdown .
class Lookable extends Behavior
{
    public function getTableList($query, $limit, $page)
    {
        $count = $query->count();
        if ($count > (($page+1) * $limit)) {
            $result = $query->offset($page * $limit)->limit($limit)->all();
            \Yii::trace($result);
            return [
              'results' => $result,
              'pagination' => [
                'more' => true,
              ]
            ];
        } else {
            $result = $query->offset($page * $limit)->all();
            \Yii::trace($result);
            return [
              'results' => $result
            ];
        }
    }
}
