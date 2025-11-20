<?php
namespace backend\controllers;

use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use common\models\User;
use common\models\users\PrivilegesUsers;
use common\models\Agent;
use common\models\UserGuardian;

class UsersController extends Controller
{
    public function actions(): array
    {
        return [
            'error'           => ['class' => 'yii\web\ErrorAction'],
            'index'           => ['class' => 'backend\actions\users\IndexAction'],
            'add-group'       => ['class' => 'backend\actions\users\AddGroupAction'],
            'add-privilege'   => ['class' => 'backend\actions\users\AddPrivilegeAction'],
            'edit-group'      => ['class' => 'backend\actions\users\EditGroupAction'],
            'edit-privilege'  => ['class' => 'backend\actions\users\EditPrivilegeAction'],
            'change-password' => ['class' => 'backend\actions\users\ChangePasswordAction'],
        ];
    }

    /* ================= AJAX ================= */

    public function actionAjaxChangeStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('iuser');
        $status = (int)Yii::$app->request->post('istatus');

        $user = User::findOne($userId);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Status nebol zmenený!'];
        }

        $user->status = $status;
        $user->save(false);

        return ['status' => 'ok', 'message' => 'Status bol zmenený!'];
    }

    public function actionAjaxChangePrivilege()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $group = Yii::$app->request->post('group');
        $priv  = Yii::$app->request->post('priv');
        $user  = Yii::$app->request->post('user');
        $st    = (int)Yii::$app->request->post('status');

        $row = PrivilegesUsers::findOne([
            'group'        => $group,
            'userId'       => $user,
            'privilegesId' => $priv,
        ]) ?? new PrivilegesUsers([
            'group'        => $group,
            'userId'       => $user,
            'privilegesId' => $priv,
            'createdAt'    => new Expression('NOW()'),
        ]);

        $row->status    = $st;
        $row->updatedAt = new Expression('NOW()');

        $tx = Yii::$app->db->beginTransaction();
        try {
            $row->save(false);
            $tx->commit();
            return ['status' => 'ok', 'message' => 'Status bol zmenený!'];
        } catch (\Throwable $e) {
            $tx->rollBack();
            return ['status' => 'error', 'message' => 'Status nebol zmenený!'];
        }
    }

    /* ================= Helpers ================= */

    private function validateGuardiansIfMinor(User $user, array $postedGuard): array
    {
        if (!$user->isMinor) {
            return [];
        }

        $errs = [];
        foreach ([0, 1] as $i) {
            $g = $postedGuard[$i] ?? [];
            $name  = trim((string)($g['name']  ?? ''));
            $phone = trim((string)($g['phone'] ?? ''));
            $email = trim((string)($g['email'] ?? ''));
            if ($name === '' || ($phone === '' && $email === '')) {
                $errs[] = "Zástupca " . ($i + 1) . ": meno a telefón alebo e-mail sú povinné.";
            }
        }
        return $errs;
    }

    /* ================= ADD ================= */

public function actionAdd()
    {
    $user = new User();
    $user->scenario = 'create';

    $g1 = new UserGuardian();
    $g2 = new UserGuardian();

    if (!Yii::$app->request->isPost) {
        $user->newPassword = null;
        $user->newPasswordRepeat = null;
    }

    if ($user->load(Yii::$app->request->post())) {

        $post        = Yii::$app->request->post();
        $postUser    = $post['User'] ?? [];
        $postedGuard = $post['Guardian'] ?? [];

        // userclassroom
        if (array_key_exists('userclassroom', $postUser)) {
            $v = trim((string)$postUser['userclassroom']);
            $user->userclassroom = ($v === '') ? null : $v;
        }

        // study_plan_type_id
        if (array_key_exists('study_plan_type_id', $postUser)) {
            $v = $postUser['study_plan_type_id'];
            $user->study_plan_type_id = ($v === '' || $v === null) ? null : (int)$v;
        }

        // User validáció
        $valid = $user->validate();

        // gyámok extra validációja, ha kiskorú
        $gErrors = $this->validateGuardiansIfMinor($user, $postedGuard);
        if ($gErrors) {
            foreach ($gErrors as $msg) {
                $user->addError('birthdate', $msg);
            }
            $valid = false;
        }

        if ($valid) {
            $tx = Yii::$app->db->beginTransaction();
            try {
                if (!$user->save(false)) {
                    throw new \RuntimeException('User create failed.');
                }

                // Gyámok mentése
                foreach ([0, 1] as $idx) {
                    $g = $postedGuard[$idx] ?? null;
                    if (!$g || trim((string)($g['name'] ?? '')) === '') {
                        continue;
                    }
                    $m = new UserGuardian();
                    $m->setAttributes([
                        'user_id'   => $user->id,
                        'name'      => trim((string)$g['name']),
                        'relation'  => (string)($g['relation']  ?? ''),
                        'phone'     => (string)($g['phone']     ?? ''),
                        'email'     => (string)($g['email']     ?? ''),
                        'street'    => (string)($g['street']    ?? ''),
                        'street_no' => (string)($g['street_no'] ?? ''),
                        'zip'       => (string)($g['zip']       ?? ''),
                        'city'      => (string)($g['city']      ?? ''),
                    ], false);
                    if (!$m->save(false)) {
                        throw new \RuntimeException('Guardian create failed.');
                    }
                }

                $tx->commit();
                Yii::$app->session->setFlash('success', 'Používateľ bol vytvorený.');
                return $this->redirect(['users/index']);
            } catch (\Throwable $e) {
                $tx->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        } else {
            Yii::$app->session->setFlash('error', 'Chyba validácie: ' . json_encode($user->errors));
        }
    }

    return $this->render('add', [
        'model' => $user,
        'g1'    => $g1,
        'g2'    => $g2,
    ]);
    }


    /* ================= EDIT ================= */

public function actionEdit($id)
    {
    $user = User::findOne($id);
    if (!$user) {
        throw new NotFoundHttpException('Používateľ nebol nájdený.');
    }
    $user->scenario = 'update';

    $guardians = $user->guardians ?? [];
    $g1 = $guardians[0] ?? new UserGuardian(['user_id' => $user->id]);
    $g2 = $guardians[1] ?? new UserGuardian(['user_id' => $user->id]);

    if (!Yii::$app->request->isPost) {
        $user->newPassword = null;
        $user->newPasswordRepeat = null;
    }

    if ($user->load(Yii::$app->request->post())) {

        $post        = Yii::$app->request->post();
        $postUser    = $post['User'] ?? [];
        $postedGuard = $post['Guardian'] ?? [];

        // --- 1) POST-ból kiszedjük a két mezőt ---
        $userclassroom = null;
        if (array_key_exists('userclassroom', $postUser)) {
            $v = trim((string)$postUser['userclassroom']);
            $userclassroom = ($v === '') ? null : $v;
        }

        $studyPlanId = null;
        if (array_key_exists('study_plan_type_id', $postUser)) {
            $v = $postUser['study_plan_type_id'];
            $studyPlanId = ($v === '' || $v === null) ? null : (int)$v;
        }

        // --- 2) Ráírjuk az AR-re ---
        $user->userclassroom      = $userclassroom;
        $user->study_plan_type_id = $studyPlanId;

        // --- 3) Mentés AR-rel ---
        $user->save(false);

        // --- 4) Nyers SQL UPDATE ugyanarra a rekordra ---
        Yii::$app->db->createCommand()->update(
            '{{%user}}',
            [
                'userclassroom'      => $userclassroom,
                'study_plan_type_id' => $studyPlanId,
            ],
            ['id' => $user->id]
        )->execute();

        // --- 5) UGYANINNEN visszaolvasunk a DB-ből ---
        $row = (new \yii\db\Query())
            ->from('{{%user}}')
            ->select(['id', 'userclassroom', 'study_plan_type_id'])
            ->where(['id' => $user->id])
            ->one();

        // --- 6) DEBUG KIÍRÁS ÉS STOP ---

    }

    return $this->render('edit', [
        'model'     => $user,
        'g1'        => $g1,
        'g2'        => $g2,
        'guardians' => $guardians,
    ]);
    }


}
