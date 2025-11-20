<?php

/**
 * @var $student \common\models\schools\Students
 */
?>
<div class="row">
    <div class="col-md-6">
        <h5 class="font-weight-bold">Zákonní zástupcovia</h5>
        <hr class="mt-1 mb-2">
        <?php if ($student->studentLegalRepresentatives): ?>
            <?php foreach ($student->studentLegalRepresentatives as $parent): ?>
                <p class="mb-1">
                    <strong>Meno:</strong> <?= htmlspecialchars($parent->firstName . ' ' . $parent->lastName) ?>
                </p>
                <p class="mb-1">
                    <strong>Email:</strong> <?= htmlspecialchars($parent->email) ?>
                </p>
                <p>
                    <strong>Telefón:</strong> <?= htmlspecialchars($parent->formattedPhone ?? $parent->phone) ?>
                </p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Žiadny zákonný zástupca nebol nájdený.</p>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <h5 class="font-weight-bold">Ostatné informácie</h5>
        <hr class="mt-1 mb-2">
        <p><strong>IBAN:</strong> <?= htmlspecialchars($student->iban ?? 'Nie je zadaný') ?></p>
        <p><strong>Adresa:</strong> <?= htmlspecialchars($student->fullAddress) ?></p>

        <h6 class="font-weight-bold mt-3">Jazyky</h6>
        <ul>
        <?php if (!empty($student->studentLanguages)) : ?>
            <?php foreach($student->studentLanguages as $lang): ?>
                <li>
                    <?= htmlspecialchars($lang->jazyk->name ?? 'Neznámy jazyk') ?>
                    (<?= $lang->motherLanguage ? 'Materinský' : 'Úroveň: ' . htmlspecialchars($lang->level) ?>)
                </li>
            <?php endforeach; ?>
        <?php else: ?>
             <li>Žiadne jazyky neboli nájdené.</li>
        <?php endif; ?>
        </ul>
    </div>
</div>