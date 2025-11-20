<?php
namespace backend\actions\documents;

use Yii;
use yii\base\Action;
use backend\models\User;
use common\models\Template;
use Mpdf\Mpdf;
use yii\helpers\FileHelper;

class GeneratorAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;

        // Handle form submission
        if ($request->isPost) {
            $userIds = $request->post('userIds', []);
            $templateHtml = $request->post('template', '');
            $selectedDate = $request->post('selected_date', date('Y-m-d'));

            $outputDir = Yii::getAlias('@backend/documents/generated');
            FileHelper::createDirectory($outputDir);

            foreach ($userIds as $userId) {
                $user = User::findOne($userId);
                if (!$user) continue;

                $replacements = [
                    '{name_first}' => $user->name_first,
                    '{name_last}'  => $user->name_last,
                    '{email}'      => $user->email,
                    '{birthdate}'  => $user->birthdate,
                    '{street}'     => $user->street,
                    '{city}'       => $user->city,
                    '{iban}'       => $user->iban,
                    '{date}'       => $selectedDate,
                ];

                $html = strtr($templateHtml, $replacements);

                $mpdf = new Mpdf();
                $mpdf->WriteHTML($html);
                $filename = $outputDir . "/document_{$user->id}_" . date('Ymd_His') . ".pdf";
                $mpdf->Output($filename, 'F');
            }

            Yii::$app->session->setFlash('success', 'Documents generated successfully!');
            return $this->controller->redirect(['documents/generator']);
        }

        // Show the form
        $users = User::find()->all();
        $templates = Template::find()->all();

        return $this->controller->render('generator', [
            'users' => $users,
            'templates' => $templates
        ]);
    }
}
