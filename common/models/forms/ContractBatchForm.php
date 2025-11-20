<?php
namespace common\models\forms;

use yii\base\Model;

class ContractBatchForm extends Model
{
    /** @var int[] */
    public $userIds = [];
    /** @var int */
    public $supervisorId;
    /** @var int */
    public $partnerId;
    /** @var string|null */
    public $companyName;
    /** @var string Y-m-d */
    public $shiftDate;
    /** @var string YYYY-MM */
    public $studyPlanMonth;

    public function rules()
    {
        return [
            [['userIds', 'supervisorId', 'partnerId', 'shiftDate', 'studyPlanMonth'], 'required'],
            [['userIds'], 'each', 'rule' => ['integer']],
            [['supervisorId', 'partnerId'], 'integer'],
            [['companyName'], 'string', 'max' => 255],
            [['shiftDate'], 'date', 'format' => 'php:Y-m-d'],
            [['studyPlanMonth'], 'match', 'pattern' => '/^\d{4}\-\d{2}$/'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'userIds'       => 'Workers',
            'supervisorId'  => 'Supervisor',
            'partnerId'     => 'Partner',
            'companyName'   => 'Company',
            'shiftDate'     => 'Shift Date',
            'studyPlanMonth'=> 'Study Plan Month (YYYY-MM)',
        ];
    }

    public function loadDefaultValues()
    {
        $this->studyPlanMonth = date('Y-m');
        $this->shiftDate = date('Y-m-d');
    }
}
