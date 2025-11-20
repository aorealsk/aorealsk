<?php
use yii\helpers\Url;
use backend\assets\RealAsset;
use common\widgets\TemplateTreeWidget;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

$this->title = "Šablóny dokumentov";
?>

<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-8 align-self-center">
      <h4 class="text-themecolor"><?= $this->title ?></h4>
    </div>
    <div class="col-md-4 align-self-center text-right">
      <div class="d-flex justify-content-end align-items-center">
        <a class="btn btn-success text-white" href="<?= Url::to(['documents/add-document']) ?>">
          <i class="fas fa-plus-circle"></i>&nbsp;<?= Yii::t('app','Pridať dokument') ?>
        </a>
        <button class="btn btn-info text-white m-l-5" id="btnAddTemplateCategory">
          <i class="fas fa-plus-circle"></i>&nbsp;<?= Yii::t('app','Pridať kategóriu') ?>
        </button>
      </div>
    </div>
  </div>

  <?= $this->render('_tabs') ?>

  <div class="tab-content">
    <!-- 📄 DOCUMENT LIST -->
    <div id="list" class="tab-pane fade in active show">
      <div class="row m-t-10">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <form method="POST" id="search">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <label class="form-control-label">
                      <h4><?= Yii::t('app', 'Vyhľadávanie dokumentov') ?></h4>
                    </label>
                    <input type="text" name="search-value" class="form-control" placeholder="Zadajte názov dokumentu...">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-3">
                    <button type="submit" class="btn btn-dark btn-block text-white">
                      <i class="fas fa-search m-r-10"></i>Hľadať
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row m-t-10">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <?= TemplateTreeWidget::widget(['id' => 'myList', 'class_id' => 'dook']); ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body" id="view-file-container"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 🆕 ADD DOCUMENT TAB -->
    <div id="addDocument" class="tab-pane fade">
      <div id="addDocumentContent" class="p-4 text-center text-muted">
        <i class="fas fa-spinner fa-spin"></i> Načítavam formulár dokumentu...
      </div>
    </div>
  </div>
</div>

<!-- ✅ Safe fallback for _modals.php -->
<?php
$modalsPath = Yii::getAlias('@backend/views/documents/_modals.php');
if (file_exists($modalsPath)) echo $this->render('_modals');
?>

<?php
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$addDocUrl = Url::to(['documents/add-document']);

$js = <<<JS
// === SEARCH FORM ===
$('#search').on('submit', function (e) {
  e.preventDefault();
  let search = $("#search input[name=search-value]").val();
  $.ajax({
    url: "/backoffice/documents/search",
    dataType: "json",
    data: {searchValue: search},
    type: "POST"
  }).done(function (res) {
    $('#search-section').remove();
    $('#search').append('<div id="search-section"><table class="table table-bordered table-striped table-sm dattable"><thead><tr><th>Názov dokumentu</th><th>Akcia</th></tr></thead><tbody id="search-results"></tbody></table></div>');
    res.forEach(function (data) {
      $('#search-results').append('<tr><td>' + data.name + '</td><td><a href="/backoffice/documents/edit?id=' + data.id + '" class="doc-edit"><i class="fas fa-edit"></i></a></td></tr>');
    });
    $('.dattable').DataTable({ order: [] });
  });
});

// === LOAD ADD DOCUMENT TAB ===
$('#btnAddTemplateCategory').on('click', function () {
  alert('Funkcia "Pridať kategóriu" ešte nie je implementovaná.');
});
JS;

$this->registerJS($js);
?>
