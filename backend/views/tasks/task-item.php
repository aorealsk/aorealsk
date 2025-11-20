<?php
/**
 * Várható változók:
 * @var mixed  $priority
 * @var string $title
 * @var string $ticketNumber
 * @var int    $ticketId
 * @var string|null $backColor  (projekt szerinti szín – fallback)
 * @var string|null $assignee
 * @var string|null $reporter
 * @var string|null $dueDate
 */

// Prioritás feliratok (id -> szöveg)
$priorityLabels = \common\models\tasks\TasksPriority::getPriorities();
$priorityLabel  = isset($priorityLabels[$priority]) ? $priorityLabels[$priority] : (string)$priority;

// Szín hozzárendelés a SZÖVEGES prioritáshoz
$priorityColorMap = [
    'Trivial'  => '#FFD54A', // világos kék
    'Major'    => '#FFA500', // narancs
    'Critical' => '#DB9D00', // eper piros / világos piros
    'Blocker'  => '#B38000', // sárga
];

// Ha nincs definiálva szín, esünk vissza a meglévő $backColor-ra
$cardBackColor = $priorityColorMap[$priorityLabel] ?? ($backColor ?? '#ffffff');
?>
<div class="ibox task-<?php echo htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?>"
     data-id="<?php echo (int)$ticketId ?>"
     style="background-color: <?= $cardBackColor ?>">

    <header class="ibox-header" style="font-weight: 500">
        <a href="/backoffice/tasks/issue?id=<?php echo (int)$ticketId ?>">
            <?php echo htmlspecialchars($ticketNumber, ENT_QUOTES, 'UTF-8') ?>
        </a>
    </header>

    <p>
        <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
    </p>

    <footer>
        <!-- Prioritás ikon + szöveg -->
        <div class="d-flex align-items-center mb-1">
            <img src="assets/images/tasks/<?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?>.svg"
                 alt="<?= htmlspecialchars($priorityLabel, ENT_QUOTES, 'UTF-8') ?>"
                 title="<?= htmlspecialchars($priorityLabel, ENT_QUOTES, 'UTF-8') ?>"
                 width="16"
                 height="16">
            <span class="ml-1 font-weight-bold">
                <?= htmlspecialchars($priorityLabel, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>

        <!-- Ki adta + kinek van kiosztva + határidő -->
        <div class="small">
            <?php if (!empty($reporter)): ?>
                <span class="mr-2">
                    <i class="mdi mdi-account-plus"></i>
                    <?= htmlspecialchars($reporter, ENT_QUOTES, 'UTF-8') ?>
                </span>
            <?php endif; ?>

            <?php if (!empty($assignee)): ?>
                <span class="mr-2">
                    <i class="mdi mdi-account-check"></i>
                    <?= htmlspecialchars($assignee, ENT_QUOTES, 'UTF-8') ?>
                </span>
            <?php endif; ?>

            <?php if (!is_null($dueDate)): ?>
                <span class="ml-2">
                    <i class="mdi mdi-clock"></i>
                    <?= htmlspecialchars($dueDate, ENT_QUOTES, 'UTF-8') ?>
                </span>
            <?php endif; ?>
        </div>
    </footer>
</div>
