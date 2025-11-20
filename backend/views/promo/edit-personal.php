<?php

/**
 * @var $promoId
 * @var $places
 * @var $personal
 */
use yii\helpers\Url;
use backend\assets\RealAsset;

$this->title = Yii::t('app', 'Editovať personál');

$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css',['depends'=>RealAsset::className()]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',['depends'=>RealAsset::className()]);
$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js',['depends'=>RealAsset::className()]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);

?>

    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <h4 class="text-themecolor"><?= $this->title ?></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <a href="<?= Url::to(["/promo/detail?id={$promoId}"]) ?>" class="btn btn-danger text-white">Späť</a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" method="post">
                            <input
                                type="hidden"
                                name="<?= Yii::$app->request->csrfParam ?>"
                                value="<?= Yii::$app->request->csrfToken ?>"
                            >
                            <input type="hidden" name="promo_id" value="<?= $promoId ?>">

                            <div class="row m-b-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Používateľské meno</label>
                                        <input type="text"
                                               class="form-control"
                                               name="user_name"
                                               value="<?= $personal->user_name ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">PIN</label>
                                        <input type="text"
                                               class="form-control check-pin"
                                               name="pin"
                                               value="<?= $personal->pin ?>"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Meno</label>
                                        <input type="text"
                                               class="form-control"
                                               name="name_first"
                                                  value="<?= $personal->name_first ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Priezvisko</label>
                                        <input type="text"
                                               class="form-control"
                                               name="name_last"
                                               value="<?= $personal->name_last ?>"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email"
                                               class="form-control"
                                               name="email"
                                               value="<?= $personal->email ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Telefón</label>
                                        <input type="tel"
                                               class="form-control"
                                               name="phone"
                                               value="<?= $personal->phone ?>"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Jazyky</label>
                                        <?php
                                        $langs = explode(',', $personal->lang);
                                        ?>
                                        <select
                                            class="form-control langs"
                                            name="lang[]"
                                            multiple>
                                            <option value="sk"<?= in_array('sk', $langs) ? ' selected' : '' ?>>
                                                SK
                                            </option>
                                            <option value="hu"<?= in_array('hu', $langs) ? ' selected' : '' ?>>
                                                HU
                                            </option>
                                            <option value="en"<?= in_array('en', $langs) ? ' selected' : '' ?>>
                                                EN
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Mzda</label>
                                        <input type="tel"
                                               class="form-control"
                                               name="wage"
                                               value="<?= $personal->wage ?>"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Pozícia</label>
                                        <select class="form-select" name="place_id">
                                            <option value="">Zvoľte...</option>
                                            <?php foreach ($places as $place) :
                                                $selected = $personal->place_id == $place->id ? ' selected' : '';
                                            ?>
                                                <option
                                                    value="<?= $place->id ?>"<?= $selected ?>
                                                >
                                                    <?= $place->place_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Pracovné zaradenie</label>
                                        <select class="form-select" name="work_position">
                                            <?php
                                            ?>
                                            <option value="">Zvoľte...</option>
                                            <option value="hostess"<?= $personal->work_position == 'hostess' ? ' selected' : ''?>>Hostess/ka</option>
                                            <option value="promoter"<?= $personal->work_position == 'promoter' ? ' selected' : ''?>>Promoter/ka</option>
                                            <option value="casnik"<?= $personal->work_position == 'casnik' ? ' selected' : ''?>>Čašník/čka</option>
                                            <option value="barman"<?= $personal->work_position == 'barman' ? ' selected' : ''?>>Barman/ka</option>
                                            <option value="kucharka"<?= $personal->work_position == 'kucharka' ? ' selected' : ''?>>Kuchár/ka</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Poznámky</label>
                                        <textarea name="note" class="form-control" cols="30" rows="10"><?= $personal->note?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success text-white">
                                        <i class="fas fa-save"></i> Uložiť
                                    </button>
                                    <a
                                        href="<?= Url::to(['/promo/detail?id=' . $promoId]) ?>"
                                        class="btn btn-danger text-white"
                                    >
                                        Späť
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<< JS
    $(document).ready(function() {
        $('.langs').select2({
            theme: 'bootstrap',
            placeholder: 'Zvoľte si jazyk(y)',
            tags: false
        });
    });

    $('.check-pin').on('change', function() {
        var pin = $(this).val();
        if (pin.length < 4) {
            $.toast({
                heading: 'Chyba',
                text: 'PIN musí mať aspoň 4 znaky',
                showHideTransition: 'slide',
                icon: 'error',
                position: 'top-right'
            });
            $(this).val('');
            return;
        }
        $.ajax({
            url: '/backoffice/promo/check-pin',
            type: 'POST',
            data: {
                pin: pin,    
                promo_id: $promoId
            },
            success: function(data) {
                if (data.status === 'error') {
                    $.toast({
                        heading: 'Chyba',
                        text: 'PIN už existuje',
                        showHideTransition: 'slide',
                        icon: 'error',
                        position: 'top-right'
                    });
                    $('.check-pin').val('');
                }
            }
        });
    });
JS;

$this->registerJS($js);