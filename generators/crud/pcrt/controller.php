<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

$foreignKeys = $generator->getForeignKeys();
/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

<?php if (count($foreignKeys) !== 0) : ?>
use pcrt\behavior\Lookable;
<?php endif; ?>
/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
<?php if(count($foreignKeys) !== 0) : ?>
            [
              'class' => Lookable::className(),
            ],
<?php endif; ?>
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
<?php if (!empty($generator->searchModelClass)): ?>    
                    'set-filter' => ['POST'],
<?php endif; ?>
<?php if(count($foreignKeys) !== 0) : ?>
<?php foreach($foreignKeys as $key): ?>
                    'get-<?=lcfirst($key['fk_table'])?>' => ['POST'],
<?php endforeach; ?>
<?php endif; ?>
                ],
            ],
        ];
    }
<?php if (!empty($generator->searchModelClass)): ?>    
    public function actionSetFilter(){
        $post = $request->post();
        $model = new <?= ltrim($generator->modelClass, '\\') ?>();
        $tablename =  <?= ltrim($generator->modelClass, '\\') ?>::model()->tableSchema->name;
        foreach($post as $key => $val){
          if(strpos($key, "filter__") !== false){
            $name = str_replace("filter__","",$key);
            $model->setFilter($name,$val);
          }
        }
        return;
    }
<?php endif; ?>
<?php foreach($foreignKeys as $key): ?>
    public function actionGet<?=ucfirst($key['fk_table'])?>()
    {

      // TODO: Adjust a text filed to mach correct field lookup
      // Adjust a query filter column to match your need
      // Also possible to add an additional filter to query
      
      $request = Yii::$app->request;
      $query = $request->post('query');
      $page = $request->post('page');
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $query = (new \yii\db\Query())
        ->select(['<?= $key['fk_field'] ?> as id', 'description as text'])
        ->from('<?= $key['fk_table'] ?>')
        ->where(['like', 'description', $query]);
      return $this->getTableList($query,20,$page-1);

    }

<?php endforeach; ?>
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(<?= $actionParams ?>)
    {
        $this->findModel(<?= $actionParams ?>)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.') ?>);
    }
}
