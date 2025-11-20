<?php
namespace common\models\tasks;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class Tasks
 *
 * @property int    $id
 * @property string $ticketNumber
 * @property string $title
 * @property string $summary
 * @property string $stage
 * @property string $taskStatus
 * @property string $priority
 * @property string $reporter
 * @property string $assignee
 * @property string $createdAt
 * @property string $updatedAt
 * @property string|null $dueDate
 *
 * @property TasksComments[] $comment
 * @property TaskCheckpoint[] $checkpoints
 * @property TasksLogTime[]  $worklogs
 */
class Tasks extends ActiveRecord
{
    const UNASSIGNED_TASK = 'unassigned';

    /**
     * Stage → taskStatus mapping
     * (backlog, inprogress, review, done → OPEN, WIP, REVIEW, DONE)
     *
     * @var array<string,int>
     */
    private $stageScenarios = [
        'backlog'    => TasksStatuses::OPEN,
        'inprogress' => TasksStatuses::WIP,
        'review'     => TasksStatuses::REVIEW,
        'done'       => TasksStatuses::DONE,
    ];

    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * Kommentek kapcsolata
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasMany(TasksComments::class, ['taskId' => 'id']);
    }

    /**
     * Checkpointok kapcsolata
     *
     * Feltételezi a task_checkpoint táblát és a TaskCheckpoint AR modellt.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCheckpoints()
    {
        // ha nincs "order" mező a táblában, nyugodtan törölheted az order kulcsot
        return $this->hasMany(TaskCheckpoint::class, ['taskId' => 'id'])
            ->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    /**
     * Worklog kapcsolata (TasksLogTime)
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorklogs()
    {
        return $this->hasMany(TasksLogTime::class, ['taskId' => 'id'])
            ->orderBy(['loggedDate' => SORT_DESC]);
    }

    /**
     * Új ticket number generálása adott projekt ID alapján.
     *
     * @param string $projectId
     * @return string
     */
    public function getNextTicketNumber(string $projectId): string
    {
        // Biztonságosabb, paraméterezett lekérdezés
        $ticketNumber = Yii::$app
            ->db
            ->createCommand(
                'SELECT ticketNumber 
                 FROM tasks 
                 WHERE ticketNumber LIKE :prefix 
                 ORDER BY id DESC 
                 LIMIT 1',
                [':prefix' => $projectId . '-%']
            )
            ->queryScalar();

        if (!$ticketNumber) {
            return "{$projectId}-1";
        }

        $parts = explode('-', $ticketNumber);
        if (!isset($parts[1]) || !ctype_digit($parts[1])) {
            // ha valami furcsa formátum jön, kezdjük 1-ről
            return "{$projectId}-1";
        }

        $parts[1] = (string)((int)$parts[1] + 1);

        return implode('-', $parts);
    }

    /**
     * Stage változtatás után frissíti a taskStatus mezőt
     * és felveszi a history-ba.
     */
    public function updateStatus(): void
    {
        $oldStatus = $this->taskStatus;

        // ha valamiért nincs stageScenario beállítva, ne dőljön el
        if (!isset($this->stageScenarios[$this->stage])) {
            return;
        }

        $user = User::findOne(['id' => Yii::$app->user->getId()]);
        $userName = $user ? $user->getUserName() : 'system';

        $this->taskStatus = $this->stageScenarios[$this->stage];
        $this->save(false, ['taskStatus']);

        TasksHistory::addToHistory(
            $this->id,
            $userName,
            'taskStatus',
            $oldStatus,
            $this->taskStatus
        );
    }

    /**
     * A címzettek listájának összeállítása e-mailhez.
     * (aktuális user, reporter, assignee)
     *
     * @param int    $taskId
     * @param string $userName
     * @return string[]
     */
    private static function getRecipients(int $taskId, string $userName): array
    {
        $result = [];

        // aktuális user
        if (($u = User::findOne(['username' => $userName])) !== null && $u->email) {
            $result[] = $u->email;
        }

        /** @var Tasks|null $task */
        $task = self::findOne(['id' => $taskId]);

        // reporter
        if ($task && $task->reporter) {
            if (($reporter = User::findOne(['username' => $task->reporter])) !== null && $reporter->email) {
                $result[] = $reporter->email;
            }
        }

        // assignee
        if ($task && $task->assignee !== null) {
            if (($assignee = User::findOne(['username' => $task->assignee])) !== null && $assignee->email) {
                $result[] = $assignee->email;
            }
        }

        return array_unique($result);
    }

    /**
     * E-mail sablon fájlnév kiválasztása.
     *
     * @param int $action
     * @return string
     */
    private static function getTemplate(int $action): string
    {
        $templates = [
            1 => 'stageUpdate-html',
        ];

        return $templates[$action] ?? 'stageUpdate-html';
    }

    /**
     * E-mail tárgy kiválasztása.
     *
     * @param int $action
     * @return string
     */
    private static function getMailSubject(int $action): string
    {
        $subjects = [
            1 => Yii::t('app', 'stage was updated'),
        ];

        return $subjects[$action] ?? Yii::t('app', 'notification');
    }

    /**
     * Értesítő e-mail küldése a feladat változásáról.
     *
     * @param int   $taskId
     * @param string $userName
     * @param int   $action
     * @param array $data
     * @return void
     */
    public static function sendNotification(int $taskId, string $userName, int $action, array $data = []): void
    {
        $template   = self::getTemplate($action);
        $subject    = 'Tasker - ' . self::getMailSubject($action);
        $mailRecips = self::getRecipients($taskId, $userName);
        $messages   = [];

        $vars = array_merge([
            'userName' => $userName,
        ], $data);

        foreach ($mailRecips as $recip) {
            $messages[] = Yii::$app
                ->mailer
                ->compose(['html' => $template], $vars)
                ->setFrom('tasker@aoreal.sk')
                ->setTo($recip)
                ->setSubject($subject);
        }

        if (!empty($messages)) {
            Yii::$app->mailer->sendMultiple($messages);
        }
    }

    public function getIssueLinks()
    {
    return $this->hasMany(TaskIssueLink::class, ['taskId' => 'id']);
    }

    public function getAttachments()
    {
    return $this->hasMany(TaskAttachment::class, ['taskId' => 'id']);
    }




}
