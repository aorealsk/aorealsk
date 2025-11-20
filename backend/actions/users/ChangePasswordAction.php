<?php
namespace backend\actions\users;

use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\User;
use backend\models\users\ChangePasswordForm;

class ChangePasswordAction extends Action
{
    /**
     * Admin jelszócsere:
     *  - /backoffice/users/change-password?uid=116  (preferált)
     *  - /backoffice/users/change-password?id=116
     * NEM esik vissza a bejelentkezett userre.
     */
    public function run()
    {
        $req = Yii::$app->request;

        // 1) uid/id GET-ből, ha nincs akkor POST-ból (hidden input)
        $targetId = $req->get('uid', $req->get('id'));
        if ($targetId === null || $targetId === '') {
            $targetId = $req->post('uid', $req->post('id'));
        }

        // 2) kötelező azonosító
        $targetId = is_scalar($targetId) ? (int)$targetId : 0;
        if ($targetId <= 0) {
            throw new BadRequestHttpException('Hiányzik a felhasználó azonosítója (uid/id).');
        }

        // 3) user betöltés
        $user = User::findOne($targetId);
        if (!$user) {
            throw new NotFoundHttpException('A felhasználó nem található.');
        }

        // 4) form feldolgozás
        $model = new ChangePasswordForm(['user' => $user]);

        if ($model->load($req->post()) && $model->change()) {
            Yii::$app->session->setFlash('success', 'A jelszó frissítve.');
            // RELATÍV route a jelenlegi controllerhez (UsersController) → modul-kompatibilis
            return $this->controller->redirect(['edit', 'uid' => $user->id]);
            // Ha inkább abszolút route-ot szeretnél, ezt használd:
            // return $this->controller->redirect(['/users/edit', 'uid' => $user->id]);
        }

        // 5) űrlap megjelenítése
        return $this->controller->render('change-password', [
            'model' => $model,
            'user'  => $user,
        ]);
    }
}
