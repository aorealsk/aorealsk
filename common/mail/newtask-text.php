<?= $projekt ?> / <?= $ticketNumber ?>


<?= $ticketTitle ?>


<?= $user ?> vytvoril nový task dňa <?= $taskDateTime ?>

-------------------------------------------------
ZÁKLADNÉ INFORMÁCIE
-------------------------------------------------
Stav:        <?= $stage      ?? '' ?>

Priorita:    <?= $priority   ?? '' ?>

Riešiteľ:    <?= $assignee   ?? ($assignee === '' ? 'unassigned' : '') ?>

Reporter:    <?= $reporter   ?? '' ?>

Termín:      <?= $dueDate    ?? '' ?>


-------------------------------------------------
POPIS
-------------------------------------------------
<?php if (!empty($summary)): ?>
<?= $summary ?>

<?php else: ?>
(bez popisu)

<?php endif; ?>

-------------------------------------------------
CHECKPOINTS
-------------------------------------------------
<?php if (!empty($checkpoints)): ?>
<?= $checkpoints ?>

<?php else: ?>
(žiadne checkpointy)
<?php endif; ?>


-------------------------------------------------
ISSUE LINKS
-------------------------------------------------
<?php if (!empty($issueLinks)): ?>
<?= $issueLinks ?>

<?php else: ?>
(žiadne issue linky)
<?php endif; ?>


-------------------------------------------------
PRÍLOHY
-------------------------------------------------
<?php if (!empty($attachments)): ?>
<?= $attachments ?>

<?php else: ?>
(žiadne prílohy)
<?php endif; ?>


-------------------------------------------------
OTVORIŤ TICKET
-------------------------------------------------
<?= $ticketUrl ?? '' ?>

