<?php
namespace common\models\users;
use Yii;
use yii\db\ActiveRecord;

class UserFile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_files';
    }

    public function afterDelete()
    {
        $fileName = Yii::getAlias('@backend')."/users/{$this->user_id}/{$this->file}";
        if(file_exists($fileName)) {
            unlink($fileName);
        }
        parent::afterDelete();
    }
}