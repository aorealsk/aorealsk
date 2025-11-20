<?php
namespace backend\actions\tasks;

use common\models\tasks\Tasks;
use common\models\tasks\TasksStages;
use common\models\tasks\TasksStatuses;
use common\models\tasks\TaskCheckpoint;
use common\models\tasks\TasksLogTime;
use common\models\users\UserGroups;
use common\models\mailer\TaskerMail;
use common\models\tasks\BoardProjects;
use common\models\tasks\TaskIssueLink;
use common\models\tasks\TaskAttachment;
use yii\web\UploadedFile;
use common\models\User;
use yii\base\Action;
use Yii;
use yii\helpers\Url;

class AddAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        // form értékek újrarendereléshez (ha hiba van)
        $data = [];

        if (Yii::$app->request->isPost) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                // fő Task mezők
                $data = Yii::$app->request->post('Task', []);

                // extra mezők az add.php formból
                $checkpointRows = Yii::$app->request->post('Checkpoints', []);
                $worklogRows    = Yii::$app->request->post('Worklog', []);

                // 1) Task mentése
                $task = $this->saveTask($data);

                // 2) Checkpointok + kezdeti worklogok mentése
                $this->saveExtras($task, $checkpointRows, $worklogRows);

                $tr->commit();

                Yii::$app->session->setFlash('success', Yii::t('app','Úloha boli pridaná'));
                return $this->controller->redirect(Url::to(['/tasks']));

            } catch (\Throwable $e) {
                $tr->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->controller->render('add',[
            'boardProjects' => BoardProjects::find()->asArray()->all(),
            'users'  => User::find()
                ->select(['username'])
                ->where(['=','status',User::STATUS_ACTIVE])
                ->asArray()
                ->all(),
            'postData'  => $data,
            'groups'    => UserGroups::find()->where(['>','type',0])->asArray()->all()
        ]);
    }

    /**
     * Létrehoz egy új Tasks rekordot a Task[...] POST adatokból.
     * Hibánál exception-t dob (amit a run() elkap).
     *
     * @param array $data
     * @return Tasks
     * @throws \RuntimeException
     */
    private function saveTask(array $data): Tasks
    {
        $task = new Tasks();

        $user = User::findOne(['id'=> Yii::$app->user->getId()]);
        $userName = $user ? $user->getUserName() : 'system';
        $now = (new \DateTimeImmutable('now'))->format("Y-m-d H:i:s");

        $task->boardId    = 0;
        $task->createdAt  = $now;
        $task->updatedAt  = $now;
        $task->updatedBy  = $userName;
        $task->reporter   = $userName;
        $task->stage      = TasksStages::BACKLOG;
        $task->taskStatus = TasksStatuses::OPEN;

        foreach ($data as $key => $value) {
            if ($key === 'project') {
                $task->ticketNumber = $task->getNextTicketNumber($value);
                continue;
            }
            $task->$key = $value;
        }

        if (!$task->save()) {
            $errors = print_r($task->getErrors(), true);
            throw new \RuntimeException('Task could not be saved: ' . $errors);
        }

        (new TaskerMail())->newTaskMail($task);

        return $task;
    }

    /**
     * Checkpointok és kezdeti worklogok mentése az új taskhoz.
     *
     * @param Tasks $task
     * @param array $checkpointRows  POST: Checkpoints[][name], [description]
     * @param array $worklogRows     POST: Worklog[][time_spent], [comment]
     */
    private function saveExtras(Tasks $task, array $checkpointRows, array $worklogRows): void
    {
    $taskId = $task->id;

    $user = User::findOne(['id'=> Yii::$app->user->getId()]);
    $userName = $user ? $user->getUserName() : 'system';
    $userId   = $user ? $user->id : null;

    // ... (CHECKPOINTS + WORKLOG – already in your file)

    /**
     * ✅ ISSUE LINKS
     * Form mezők:
     *   IssueLinks[][type]
     *   IssueLinks[][issueKey]
     */
    $issueLinks = Yii::$app->request->post('IssueLinks', []);
    foreach ($issueLinks as $row) {
        $type = trim($row['type'] ?? '');
        $key  = trim($row['issueKey'] ?? '');

        if ($type === '' && $key === '') {
            continue;
        }

        $link = new TaskIssueLink();
        $link->taskId   = $taskId;
        $link->type     = $type;
        $link->issueKey = $key;
        $link->createdAt = date('Y-m-d H:i:s');
        $link->save(false);
    }

    /**
     * ✅ ATTACHMENTS
     * Form mező:
     *   Attachments[] (file input, multiple)
     */
    $files = UploadedFile::getInstancesByName('Attachments');
    if (!empty($files)) {
        $basePath = Yii::getAlias('@webroot/uploads/tasks/' . $taskId);
        if (!is_dir($basePath)) {
            @mkdir($basePath, 0775, true);
        }

        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/','_', $file->name);
            $path = $basePath . '/' . $safeName;
            if ($file->saveAs($path)) {
                $att = new TaskAttachment();
                $att->taskId     = $taskId;
                $att->fileName   = $file->name;
                $att->filePath   = 'uploads/tasks/' . $taskId . '/' . $safeName; // relative path
                $att->uploadedBy = $userName;
                $att->uploadedAt = date('Y-m-d H:i:s');
                $att->save(false);
            }
        }
    }
    }
}
