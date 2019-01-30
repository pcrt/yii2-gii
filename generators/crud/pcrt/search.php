<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();
$fields = $generator->getSearchField();


echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
 * <?= $searchModelClass ?> represents the model behind the search form of `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // filtering conditions

<?php foreach($fields as $key => $field): ?>
<?php if(($field['type']==="integer" && $field['fk'] === []) || $field['type']==="double" || $field['type']==="float"): ?>
        $<?= $key ?> = $this->getFilter('<?= $key ?>');
        if($<?= $key ?> !== "" && $<?= $key ?> !== null){
          $query->andFilterWhere(['=', '<?= $key ?>', $<?= $key ?>);
        }
<?php endif; ?>
<?php if($field['type']==="string" || $field['type']==="text"): ?>
        $<?= $key ?> = $this->getFilter('<?= $key ?>');
        if($<?= $key ?> !== "" && $<?= $key ?> !== null){
          $query->andFilterWhere(['LIKE', '<?= $key ?>', $<?= $key ?>);
        }
<?php endif; ?>
<?php if($field['type']==="date" || $field['type']==="datetime"): ?>
        $<?= $key ?> = explode("|", $this->getFilter('<?= $key ?>'));
        if(count($<?= $key ?>) === 2){
          $query->andFilterWhere(['BETWEEN', '<?= $key ?>', $<?= $key ?>[0], $<?= $key ?>[1]]);
        }
<?php endif; ?>
<?php if($field['type']==="integer" && $field['fk'] !== []): ?>
        $<?= $key ?> = $this->getFilter('<?= $key ?>');
        if($<?= $key ?> !== "" && $<?= $key ?> !== null){
          $query->andFilterWhere(['IN', '<?= $key ?>', $<?= $key ?>]);
        }
<?php endif; ?>
<?php endforeach; ?>
        return $dataProvider;
    }
}
