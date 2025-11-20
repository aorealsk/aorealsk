<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class PdfTemplate
 *
 * Table: pdf_template
 *
 * @property int         $id
 * @property string      $name       Human readable name
 * @property string      $file_path  Web-relative path, e.g. "/uploads/pdf_templates/tpl_xxx.pdf"
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PdfTemplate extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        // IMPORTANT: your table is named `pdf_template`
        return 'pdf_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'file_path'], 'required'],

            [['created_at', 'updated_at'], 'safe'],

            [['name'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'name'       => 'Názov šablóny',
            'file_path'  => 'Cesta k PDF súboru',
            'created_at' => 'Vytvorené',
            'updated_at' => 'Upravené',
        ];
    }
}
