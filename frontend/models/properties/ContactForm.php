<?php

namespace frontend\models\properties;

use yii\base\Model;

class ContactForm extends Model
{
    public $name;
    public $surname;
    public $email;
    public $phone;
    public $note;
    public $verifyCode;

    public function rules()
    {
        return [
            [['name', 'surname', 'email', 'phone'], 'required','message' => 'Povinné pole'],
            [['name', 'surname'], 'string', 'min' => 2, 'max' => 255],
            ['phone', 'string', 'min' => 9, 'max' => 15],
            ['email', 'email'],
            ['note', 'string', 'max' => 2000],
            ['verifyCode', 'captcha'],
        ];
    }
}