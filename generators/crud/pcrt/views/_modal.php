<?php
echo "<?php\n";
?>
use yii\bootstrap4\Modal;

Modal::begin([
     'title' => '<h2></h2>',
     'toggleButton' => ['label' => 'Chiudi'],
]);

$this->renderPartial('_form');

Modal::end();
