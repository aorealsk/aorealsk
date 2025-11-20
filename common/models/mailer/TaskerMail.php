<?php
namespace common\models\mailer;

use Yii;
use common\models\tasks\Tasks;
use common\models\tasks\TasksProject;
use common\models\User;

class TaskerMail extends AoMailer
{
    const FORMAT_TEXT = 'text';
    const FORMAT_HTML = 'html';

    /**
     * New task created.
     *
     * @param Tasks  $task
     * @param string $format
     */
    public function newTaskMail(Tasks $task, string $format = self::FORMAT_TEXT): void
    {
        $project = $this->getTaskProject($task->ticketNumber);

        // base data for this email
        $data = [
            'ticketNumber' => $task->ticketNumber,
            'user'         => $task->reporter,
            'taskDateTime' => $task->createdAt,
            'projekt'      => $project ? $project->name : '',
            'ticketTitle'  => $task->title,
        ];

        // ✚ add full ticket details
        $data = array_merge($data, $this->getFullTicketData($task));

        $this->setData($data);

        $emails = $this->getUserEmails($task);
        $this->setSubject('[TASKER] ' . Yii::t('app','Nový task ') . $task->ticketNumber . ': ' . $task->title);
        $this->setRecipients($emails);
        $this->setTemplate('newtask-text');
        $this->sendTextMessage();
    }

    /**
     * Stage update.
     */
    public function updateStageMail(
        Tasks $task,
        string $oldStage,
        string $newStage,
        string $user,
        string $format = self::FORMAT_TEXT
    ): void
    {
        $project = $this->getTaskProject($task->ticketNumber);

        $data = [
            'ticketNumber' => $task->ticketNumber,
            'user'         => $user,
            'projekt'      => $project ? $project->name : '',
            'ticketTitle'  => $task->title,
            'oldStage'     => $oldStage,
            'newStage'     => $newStage,
            'updateDate'   => $task->updatedAt,
        ];

        // ✚ add full ticket details
        $data = array_merge($data, $this->getFullTicketData($task));

        $this->setData($data);

        $emails = $this->getUserEmails($task);
        $this->setSubject('[TASKER] ' . Yii::t('app','Update task ') . $task->ticketNumber . ': ' . $task->title);
        $this->setRecipients($emails);
        $this->setTemplate('updatestage-text');
        $this->sendTextMessage();
    }

    /**
     * Priority update.
     */
    public function updatePriorityMail(
        Tasks $task,
        string $oldPriority,
        string $newPriority,
        string $user,
        string $format = self::FORMAT_TEXT
    ): void
    {
        $project = $this->getTaskProject($task->ticketNumber);

        $data = [
            'ticketNumber' => $task->ticketNumber,
            'user'         => $user,
            'projekt'      => $project ? $project->name : '',
            'ticketTitle'  => $task->title,
            'oldPriority'  => $oldPriority,
            'newPriority'  => $newPriority,
            'updateDate'   => $task->updatedAt,
        ];

        // ✚ add full ticket details
        $data = array_merge($data, $this->getFullTicketData($task));

        $this->setData($data);

        $emails = $this->getUserEmails($task);
        $this->setSubject('[TASKER] ' . Yii::t('app','Update task ') . $task->ticketNumber . ': ' . $task->title);
        $this->setRecipients($emails);
        $this->setTemplate('updatepriority-text');
        $this->sendTextMessage();
    }

    /**
     * Assignee update.
     */
    public function updateAssigneeMail(
        Tasks $task,
        string $oldAssignee,
        string $newAssignee,
        string $format = self::FORMAT_TEXT
    ): void
    {
        $project = $this->getTaskProject($task->ticketNumber);

        $data = [
            'ticketNumber' => $task->ticketNumber,
            'user'         => $task->reporter,
            'projekt'      => $project ? $project->name : '',
            'ticketTitle'  => $task->title,
            'oldAssignee'  => $oldAssignee,
            'newAssignee'  => $newAssignee,
            'updateDate'   => $task->updatedAt,
        ];

        // ✚ add full ticket details
        $data = array_merge($data, $this->getFullTicketData($task));

        $this->setData($data);

        $emails = $this->getUserEmails($task);
        $this->setSubject('[TASKER] ' . Yii::t('app','Update task ') . $task->ticketNumber . ': ' . $task->title);
        $this->setRecipients($emails);
        $this->setTemplate('updateassignee-text');
        $this->sendTextMessage();
    }

    /**
     * Build list of recipients: reporter + assignee (if any).
     */
    private function getUserEmails(Tasks $task): array
    {
        $emails = [];

        $user = User::findOne(['username'=>$task->reporter]);
        if ($user && $user->email) {
            $emails[] = $user->email;
        }

        if (!is_null($task->assignee) && $task->assignee != Tasks::UNASSIGNED_TASK) {
            $user = User::findOne(['username'=>$task->assignee]);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        return array_unique($emails);
    }

    /**
     * Determine project from ticket number.
     */
    private function getTaskProject(string $ticketNumber): ?TasksProject
    {
        $code = (explode('-',$ticketNumber))[0] ?? null;
        if (!$code) {
            return null;
        }
        return TasksProject::findOne(['code'=>$code]);
    }

    /**
     * ✚ Extra helper: return ALL ticket data we want to show in emails
     * as plain-text strings for the templates.
     */
    private function getFullTicketData(Tasks $task): array
    {
        // description without HTML
        $summaryPlain = trim(strip_tags((string)$task->summary));

        // checkpoints as multi-line bullet list
        $checkpointLines = [];
        if (property_exists($task, 'checkpoints') || method_exists($task, 'getCheckpoints')) {
            foreach ($task->checkpoints as $cp) {
                $label = trim((string)$cp->label);
                $chk   = !empty($cp->isDone) ? 'x' : ' ';
                $checkpointLines[] = sprintf('[%s] %s', $chk, $label);
            }
        }
        $checkpointsText = empty($checkpointLines) ? '' : implode("\n", $checkpointLines);

        // issue links list
        $issueLinkLines = [];
        if (method_exists($task, 'getIssueLinks')) {
            foreach ($task->issueLinks as $link) {
                $type = trim((string)$link->type);
                $key  = trim((string)$link->issueKey);
                $issueLinkLines[] = ($type !== '' ? $type . ': ' : '') . $key;
            }
        }
        $issueLinksText = empty($issueLinkLines) ? '' : implode("\n", $issueLinkLines);

        // attachments list
        $attachmentLines = [];
        if (method_exists($task, 'getAttachments')) {
            foreach ($task->attachments as $att) {
                $line = (string)$att->fileName;
                if ($att->uploadedBy) {
                    $line .= ' – ' . $att->uploadedBy;
                }
                $attachmentLines[] = $line;
            }
        }
        $attachmentsText = empty($attachmentLines) ? '' : implode("\n", $attachmentLines);

        // direct link to the ticket in backoffice
        $ticketUrl = Yii::$app->urlManager->createAbsoluteUrl([
            '/tasks/issue',
            'id' => $task->id,
        ]);

        return [
            // main fields
            'stage'       => $task->stage,
            'priority'    => $task->priority,
            'assignee'    => $task->assignee ?: Tasks::UNASSIGNED_TASK,
            'reporter'    => $task->reporter,
            'dueDate'     => $task->dueDate,
            'summary'     => $summaryPlain,

            // collections
            'checkpoints' => $checkpointsText,
            'issueLinks'  => $issueLinksText,
            'attachments' => $attachmentsText,

            // link back to app
            'ticketUrl'   => $ticketUrl,
        ];
    }
}
