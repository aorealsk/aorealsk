<?php

use yii\helpers\Url;
use backend\helpers\HelpersNum;
use common\models\accounting\invoice\InvoiceType;
use common\models\Invoice;
use common\helpers\MoneyHelper;

/**
 * @var $invoiceSummary
 * @var $office
 * @var $year
 */

?>

<div class="card rounded-5 card-shadow">
    <div class="card-body">
        <h4 class="card-title" style="margin-bottom: 40px"><?php echo $office['name'] ?></h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><strong>Sumár (<?= $year ?>)</strong></th>
                    <th><strong>Limit</strong></th>
                    <th><strong>Zostatok</strong></th>
                    <th>
                        <strong>Sumár za posledných 12 mes.</strong>
                    </th>
                    <th><strong>Limit na DPH</strong></th>
                    <th><strong>Zostatok do limitu</strong></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&euro; <?= MoneyHelper::displayMoney($invoiceSummary['yearly_total']) ?></td>
                    <td>&euro; 49790,00</td>
                    <td>&euro;<?= MoneyHelper::displayMoney(49790 - $invoiceSummary['yearly_total']) ?></td>
                    <td>&euro; <?= MoneyHelper::displayMoney($invoiceSummary['last_12m_total'])  ?></td>
                    <td>&euro; 49790,00</td>
                    <td>&euro;<?= MoneyHelper::displayMoney(49790 - $invoiceSummary['last_12m_total']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-bordered table-striped table-sm dattable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Číslo</th>
                            <th>Typ</th>
                            <th>Partner</th>
                            <th>K úhrade</th>
                            <th>Záloha</th>
                            <th>Vystavené</th>
                            <th>Splatnosť</th>
                            <th>Status</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($office['invoices'] as $invoice) : ?>
                    <tr id="rw-<?= $invoice['id'] ?>">
                        <td class="text-center">
                            <?php
                            $icon = $invoice['bookedIn'] === 'odfa' ?
                                'mdi mdi-arrow-left-bold text-success' :
                                'mdi mdi-arrow-right-bold text-danger';
                            ?>
                            <i class="<?= $icon ?>"></i>
                        </td>
                        <td>
                            <?= $invoice['znak']?><?= $invoice['rok']?><?= $invoice['mesiac']?><?= $invoice['cislo']?>
                            <br>
                            <span class="font-10 text-muted">(ID: <?php echo $invoice['id'] ?>)</span>
                        </td>
                        <td>
                            <?php
                            switch ((int)$invoice['typ_faktury']) {
                                case InvoiceType::INVOICE:
                                    $typ = 'FA';
                                    $typPopis = 'Faktúra';
                                    break;
                                case InvoiceType::DEPOSIT_INVOICE:
                                    $typ = 'ZAFA';
                                    $typPopis = 'Zálohová faktúra';
                                    break;
                                default:
                                    $typ = 'NEID';
                                    $typPopis = 'Neidentifikované';
                            }
                            ?>
                            <span title="<?= $typPopis ?>" style="cursor: pointer;"><?= $typ ?></span>
                        </td>
                        <td>
                            <?= ($invoice['odberatel'] != '') ? $invoice['odberatel'] : $invoice['kontaktna_osoba']?>
                        </td>
                        <td style="text-align: right">
                            <?= HelpersNum::moneyFormat($invoice['k_uhrade']) ?>
                        </td>
                        <td style="text-align: right">
                            <?= HelpersNum::moneyFormat($invoice['zaloha']) ?>
                        </td>
                        <td style="text-align: right">
                            <?= $invoice['datum_vystavenia'] ?>
                        </td>
                        <td style="text-align: right">
                            <?= $invoice['splatnost'] ?>
                        </td>
                        <td>
                            <input
                                    type="checkbox"
                                    class="js-switch"
                                    data-color="#26c6da"
                                    data-secondary-color="#f62d51"
                                    data-invoice="<?= $invoice['id'] ?>"
                                    <?= (int)$invoice['status'] === Invoice::PAID ? ' checked' : '' ?>
                            >
                        </td>
                        <td>
                            <?php if ((int)$invoice['typ_faktury'] === InvoiceType::DEPOSIT_INVOICE) : ?>
                                <a
                                        href="<?= Url::to(['accounting/make-invoice','id' => $invoice['id']]) ?>"
                                        style="color: black; margin-right: 5px;"
                                        title="Vytvoriť faktúru')"
                                >
                                    <i class="fas fa-file-medical"></i>
                                </a>
                            <?php endif; ?>
                            <?php
                            //if ($invoice['bookedIn'] == 'odfa') {
                            ?>
                            <a
                                    href= "<?= Url::to(['accounting/print',
                                        't' => InvoiceType::getInvoiceTypeCode($invoice['typ_faktury']),
                                        'id' => $invoice['id']]) ?>"
                                    title="Print"
                                    style="color: black;margin-right: 5px;"
                            >
                                <i class="fas fa-print"></i>
                            </a>
                            <?php
                            //}
                            ?>
                            <a href="<?= Url::to(['accounting/edit-invoice','id' => $invoice['id']]) ?>"
                               title="Edit" style="color: black">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <?php if ($invoice['typ_faktury'] == InvoiceType::DEPOSIT_INVOICE) : ?>
                            <a href="#" data-invoice="<?= $invoice['id'] ?>" title="Zmazať"
                               style="color: black" class="deposit-act">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>
