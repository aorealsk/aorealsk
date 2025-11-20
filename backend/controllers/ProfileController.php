<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

use common\models\User;
use common\models\Agent;
use common\models\users\UserDetails;

// Adjust this to wherever you placed the form model I suggested.
// If you used another namespace/path, update the use line accordingly.
use backend\models\forms\UserProfileForm;

class ProfileController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionDownload(string $f)
    {
    // Must be logged in
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['/site/login']);
    }

    // Current user's username -> folder
    $username = (string)(Yii::$app->user->identity->username ?? 'user');
    $safeUser = preg_replace('~[^a-zA-Z0-9._-]~', '_', $username);

    // Base directory: /uploads/users/USERNAME/documents
    $baseDir = Yii::getAlias('@webroot') . "/uploads/users/{$safeUser}/documents";
    $baseReal = realpath($baseDir);
    if ($baseReal === false) {
        throw new \yii\web\NotFoundHttpException('Zložka s dokumentmi neexistuje.');
    }

    // Sanitize requested file. We only allow plain filenames in the documents folder.
    $fileName = basename($f); // strips any path attempts
    $absPath  = $baseReal . DIRECTORY_SEPARATOR . $fileName;
    $absReal  = realpath($absPath);

    // Security checks
    if ($absReal === false || strpos($absReal, $baseReal) !== 0 || !is_file($absReal)) {
        throw new \yii\web\NotFoundHttpException('Súbor nebol nájdený.');
    }

    // Force download
    return Yii::$app->response->sendFile(
        $absReal,
        $fileName,
        ['inline' => false] // attachment
    );
    }


    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/site/login']));
        }

        $userId = (int)Yii::$app->user->getId();

        if (Yii::$app->request->isPost) {
            $toUpdate = (string)Yii::$app->request->post('toupdate', '');
            $data     = Yii::$app->request->post();

            $tr = Yii::$app->db->beginTransaction();
            try {
                switch ($toUpdate) {
                    case 'profile':
                        // Update basic profile fields via your identity helper
                        Yii::$app->user->identity->updateProfileData($data);
                        Yii::$app->user->identity->save(false);

                        // Keep Agent in sync (legacy) – consider removing once migrated
                        $agent = Agent::findOne(['user_id' => $userId]);
                        if ($agent) {
                            if (isset($data['name_first']) && $agent->name_first !== trim($data['name_first'])) {
                                $agent->name_first = trim($data['name_first']);
                            }
                            if (isset($data['name_last']) && $agent->name_last !== trim($data['name_last'])) {
                                $agent->name_last = trim($data['name_last']);
                            }
                            if (isset($data['phone']) && $agent->phone !== trim($data['phone'])) {
                                $agent->phone = trim($data['phone']);
                            }
                            $agent->save(false);
                            unset($agent);
                        }

                        // Update user details (social links etc.)
                        $details = UserDetails::findOne(['userId' => $userId]);
                        if (!$details) {
                            $details = new UserDetails();
                            $details->userId = $userId;
                        }
                        foreach (['facebook', 'twitter', 'linkedin', 'instagram', 'youtube'] as $network) {
                            if (array_key_exists($network, $data)) {
                                $val = trim((string)$data[$network]);
                                if ($details->$network !== $val) {
                                    $details->$network = $val;
                                }
                            }
                        }
                        $details->save(false);
                        unset($details);

                        Yii::$app->session->setFlash('success', Yii::t('app', 'Údaje na profile boli úspešne zmenené!'));
                        break;

                    case 'password':
                        // Basic guard: ignore empty password submissions
                        $pwd = (string)($data['password'] ?? '');
                        if ($pwd !== '') {
                            Yii::$app->user->identity->setPassword($pwd);
                            Yii::$app->user->identity->save(false);
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Heslo bolo úspešne zmenené!'));
                        } else {
                            Yii::$app->session->setFlash('warning', Yii::t('app', 'Heslo nemôže byť prázdne.'));
                        }
                        break;

                    case 'pic':
                        // Safer upload using UploadedFile
                        $file = UploadedFile::getInstanceByName('profilePic');
                        if ($file) {
                            $targetDir = Yii::getAlias('@webroot') . "/../../media/profiles/{$userId}";
                            if (!is_dir($targetDir)) {
                                @mkdir($targetDir, 0775, true);
                            }

                            // Sanitize filename
                            $safeBase = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->baseName);
                            $filename = $safeBase . '.' . $file->extension;

                            if ($file->saveAs($targetDir . '/' . $filename, false)) {
                                $detail = UserDetails::findOne(['userId' => $userId]);
                                if (!$detail) {
                                    $detail = new UserDetails();
                                    $detail->userId = $userId;
                                }
                                $detail->profilePic = $filename;
                                $detail->save(false);
                                unset($detail);

                                Yii::$app->session->setFlash('success', Yii::t('app', 'Fotka bola úspešne zmenená!'));
                            } else {
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Nepodarilo sa uložiť fotku.'));
                            }
                        } else {
                            Yii::$app->session->setFlash('warning', Yii::t('app', 'Nebola vybratá žiadna fotka.'));
                        }
                        break;
                }

                $tr->commit();
                return $this->redirect(['/profile']);
            } catch (\Throwable $e) {
                if ($tr->isActive) {
                    $tr->rollBack();
                }
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('index', [
            'userId'      => $userId,
            'user'        => Yii::$app->user,
            'agent'       => Agent::findOne(['user_id' => $userId]),
            'userDetails' => UserDetails::findOne(['userId' => $userId]),
        ]);
    }

    public function actionEdit()
    {
    /** @var User $u */
    $u = Yii::$app->user->identity;

    // Build the form model from current user
    $model = UserProfileForm::fromUser($u);

    // Make sure the form shows the current value even if fromUser() didn't map it
    if (property_exists($model, 'userclassroom') && $model->userclassroom === null) {
        $model->userclassroom = $u->userclassroom ?? null;
    }

    if ($model->load(Yii::$app->request->post())) {
        // Normalize posted payload safely
        $posted = Yii::$app->request->post('UserProfileForm', []);

        // Guardians may come as nested array – keep your existing handling
        if (isset($posted['guardians'])) {
            $model->guardians = (array)$posted['guardians'];
        }

        // Pass through userclassroom (trim; allow empty -> null)
        if (array_key_exists('userclassroom', $posted)) {
            $val = is_string($posted['userclassroom']) ? trim($posted['userclassroom']) : $posted['userclassroom'];
            $model->userclassroom = ($val === '') ? null : $val;
        }

        if ($model->validate() && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Profil bol uložený.'));
            return $this->redirect(['edit']);
        }
    }

    return $this->render('edit', ['model' => $model]);
    }

}
