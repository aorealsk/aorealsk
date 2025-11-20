<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\Teacher;

class TeacherSearch extends Model
{
    public $FirstName;
    public $LastName;        // <-- hiányzott a "public"
    public $EmailAddress;
    public $PhoneNumber;
    public $SchoolID;
    public $PrimaryStudy;

    public function rules()
    {
        return [
            [['FirstName','LastName','EmailAddress','PhoneNumber','PrimaryStudy'], 'safe'],
            [['SchoolID'], 'integer'],
        ];
    }

    public function search($params)
    {
        $t = Teacher::tableName(); // "Teacher"
        $query = Teacher::find()->joinWith('school'); // hogy tudjunk iskolanévre szűrni/nézni

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['LastName' => SORT_ASC, 'FirstName' => SORT_ASC],
                'attributes' => [
                    'FirstName',
                    'LastName',
                    'EmailAddress',
                    'PhoneNumber',
                    'PrimaryStudy',
                    'SchoolID' => [
                        // a Partners AR tábla neve nálatok nagy eséllyel "partners"
                        'asc'  => ['partners.partner_name' => SORT_ASC],
                        'desc' => ['partners.partner_name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // szűrések
        $query->andFilterWhere(["$t.SchoolID" => $this->SchoolID]);
        $query->andFilterWhere(['like', "$t.FirstName", $this->FirstName])
              ->andFilterWhere(['like', "$t.LastName",  $this->LastName])
              ->andFilterWhere(['like', "$t.EmailAddress", $this->EmailAddress])
              ->andFilterWhere(['like', "$t.PhoneNumber",  $this->PhoneNumber])
              ->andFilterWhere(['like', "$t.PrimaryStudy", $this->PrimaryStudy]);

        return $dataProvider;
    }
}
