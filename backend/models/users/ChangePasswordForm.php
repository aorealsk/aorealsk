<?php
namespace backend\models\users;

use yii\base\Model;
use common\models\User;

class ChangePasswordForm extends Model
{
    public ?User $user = null;
    public string $new_password = '';
    public string $new_password_repeat = '';

    public function rules(): array
    {
        return [
            [['new_password','new_password_repeat'],'required'],
            [['new_password','new_password_repeat'],'string','min'=>8],
            ['new_password_repeat','compare','compareAttribute'=>'new_password','message'=>'A jelszavak nem egyeznek.'],
        ];
    }

    public function change(): bool
    {
        if (!$this->user) {
            $this->addError('new_password','Felhasználó nem található.');
            return false;
        }
        if (!$this->validate()) return false;

        $this->user->setPassword($this->new_password);
        if (method_exists($this->user,'generateAuthKey')) {
            $this->user->generateAuthKey(); // érvényteleníti a régi sessionöket
        }
        return $this->user->save(false);
    }
}
