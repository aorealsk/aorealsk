<?php

use backend\assets\RealAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\security\SecuritySupport;

RealAsset::register($this);

$uid = Yii::$app->user->id ?? 0;
// This builds: /backoffice/studenttest.php?uid=123
$quizUrl = Yii::$app->request->baseUrl . '/studenttest.php?uid=' . (int)$uid;
$username = !isset(Yii::$app->user->identity->username) ? "Guest" : Yii::$app->user->identity->username;
$profilePicture = Yii::$app->user->identity->getProfilePicture();
$userId = Yii::$app->user->getId();
$active = (strpos($_SERVER['REQUEST_URI'], 'tasks')) ? ' active' : '';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <?php $this->head() ?>
</head>
<body class="skin-blue fixed-layout">
    <?php $this->beginBody() ?>
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/backoffice/" style="font-size:0.8375rem !important">
                        <b>
                            <img
                                    src="<?= Yii::getAlias('@web') ?>/assets/images/logo-light-icon.png"
                                    alt="homepage" class="light-logo" />
                        </b>
                        <span class="hidden-xs">Alpha-Omega&nbsp;Reality</span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a
                                class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark"
                                href="javascript:void(0)">
                                <i class="ti-menu"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark"
                                href="javascript:void(0)">
                                <i class="icon-menu"></i>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown u-pro">
                            <a
                                class="nav-link dropdown-toggle waves-effect waves-dark profile-pic"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?= $profilePicture ?>" alt="<?= $username ?>" class="img-circle">
                                <span class="hidden-md-down">
                                    <?= $username ?> &nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated flipInY">
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="ti-user"></i> Môj profil</a>
                                <a href="/backoffice/site/logout" class="dropdown-item" data-method="post">
                                    <i class="fa fa-power-off"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li>
                            <a href="/backoffice/profile" class="waves-effect waves-dark">
                                <i class="ti-user"></i><span class="hide-menu">Môj profil</span>
                            </a>
                        </li>
                        <li>
                            <a href="/backoffice/user-attendance?uid=<?= $userId ?>">
                                <i class="ti-alarm-clock"></i><span class="hide-menu">Moja dochádzka</span>
                            </a>
                        </li>
                        <li>
                            <a href="/backoffice/calendar" class="waves-effect waves-dark">
                                <i class="fas fa-calendar-alt"></i><span class="hide-menu">Kalendár</span>
                            </a>
                        </li>
                        <li>
                            <a href="/backoffice/tasks" class="waves-effect waves-dark">
                                <i class="fas fa-tasks"></i><span class="hide-menu">Úlohy</span>
                            </a>
                        </li>
                        <?php if (SecuritySupport::canDo('realities')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="icon-home"></i><span class="hide-menu">Reality</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/backoffice/property-register">Register</a></li>
                                <li><a href="/backoffice/contracts">Nehnuteľnosti</a></li>
                                <li><a href="/backoffice/offers">Ponuky</a></li>
                                <!--<li><a href="/backoffice/external-offers">Externé ponuky</a></li>-->
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('customers')) : ?>
                        <li>
                            <a class="waves-effect waves-dark" href="/backoffice/customers">
                                <i class="ti-user"></i><span class="hide-menu">Zákaznící</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('documents')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="far fa-folder"></i><span class="hide-menu">Dokumenty</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/backoffice/documents">Zoznam</a></li>
                                <li><a href="/backoffice/template-vars">Premenné</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('promotions')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="fas fa-bullhorn"></i>
                                <span class="hide-menu">Akcie</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/backoffice/promo">Zoznam</a></li>
                                <li><a href="/backoffice/promo/stock">Sklad</a></li>
                                <li><a href="/backoffice/promo/places">Pozície</a></li>
                                <li><a href="/backoffice/promo/codes">Promo kódy</a></li>
                                <li><a href="/backoffice/promo/referrals">Referal kódy</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('services')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                                </svg>
                                <span class="hide-menu ml-2">Služby</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="/backoffice/services/index">
                                        <?= Yii::t('app', 'Zoznam služieb') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="/backoffice/units/index">
                                        <?= Yii::t('app', 'Merné jednotky') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('trainees')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="fas fa-users"></i><span class="hide-menu">Prax</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="/backoffice/students"><span class="hide-menu">Študenti</span></a>
                                </li>
                                <!-- UPDATED: everyone goes to the Mentor/Teams workspace -->
                                <a class="nav-link" href="/studenttest.php?uid=<?= (int)$uid ?>" target="_blank">
                                    <i class="hide-menu"></i> Test bdelosti
                                </a>
                                <li>
                                    <a href="<?= Url::to(['/mentor/teams']) ?>"><span class="hide-menu">Učitelia</span></a>
                                </li>
                                <!-- Helpful shortcuts for everyone -->
                                <li>
                                    <a href="<?= Url::to(['/mentor/profile']) ?>"><span class="hide-menu">Môj profil (učiteľ/partner)</span></a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['/mentor/teams']) ?>"><span class="hide-menu">Moje tímy</span></a>
                                </li>

                                <li>
                                    <a href="/backoffice/user-attendance-admin"><span class="hide-menu">Dochádzka</span></a>
                                </li>
                                <li>
                                    <a href="/backoffice/trainee/reports"><span class="hide-menu">Reporty</span></a>
                                </li>
                                <!--<li>
                                    <span class="hide-menu"><a href="/backoffice/trainee/settings">Nastavenia</a></span>
                                </li>-->
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('accounting')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="fas fa-balance-scale"></i><span class="hide-menu">
                                    <?= Yii::t('app', 'Účtovníctvo') ?>
                                </span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <?php if (SecuritySupport::canDo('accounting.invoices')) : ?>
                                <li><a href="/backoffice/accounting/invoice">Faktúry</a></li>
                                <?php endif; ?>
                                <?php if (SecuritySupport::canDo('accounting.pokdok')) : ?>
                                <li><a href="/backoffice/accounting/cash-receipt">Pokladničné dokl.</a></li>
                                <?php endif; ?>
                                <li>
                                    <a href="/backoffice/accounts">
                                        <?= Yii::t('app', 'Firemné účty') ?>
                                    </a>
                                </li>
                              </ul>
                        </li>
                        <?php endif; ?>
                        <?php if (SecuritySupport::canDo('settings')) : ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <i class="ti-settings"></i><span class="hide-menu">Nastavenia</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/backoffice/users">Užívatelia</a></li>
                                <li><a href="/backoffice/task-manager">Manažér úloh</a></li>
                                <li> <a href="/backoffice/template">Dokumenty</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="/backoffice/site/logout" class="dropdown-item" data-method="post">
                                <i class="fa fa-power-off"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- ADDED responsive padding-left (and a bit of right) so content doesn't hug the sidebar -->
        <div class="page-wrapper pl-3 pl-md-4 pl-xl-5 pr-3">
            <?php /** @var $content */ ?>
            <?= $content ?>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
