<?php
/**
 * @var $clients
 * @var $mesto
 */
?>
<div class="form-group row">
    <div class="col-10">
        <select class="form-select" id="get-odber">
            <option value=""></option>
            <?php foreach($clients as $idx => $it): ?>
                <option value='<?= $it ?>'><?= $idx ?></option>";
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-2">
        <button class="btn btn-dark" type="button" id="odber-reset">Reset</button>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-name">Spoločnosť:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[nazov]" class="form-control" id="odberatel-name" value="<?= $odberatelData['nazov'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-contactperson">Osoba:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[kontaktna_osoba]" class="form-control" id="odberatel-contactperson" value="<?= $odberatelData['kontaktna_osoba'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odber-email">Email:</label>
    <div class="col-9">
        <input type="email" name="Odberatel[email]" class="form-control" value="<?= $odberatelData['email'] ?? '' ?>" id="odber-email">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odber-phone">Telefón:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[phone]" class="form-control" value="<?= $odberatelData['phone'] ?? '' ?>" id="odber-phone">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odber-web">Web:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[web]" class="form-control" value="<?= $odberatelData['web'] ?? '' ?>" id="odber-web">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-address">Ulica:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[ulica]" class="form-control" id="odberatel-address" value="<?= $odberatelData['ulica'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-town">Mesto:</label>
    <div class="col-9">
        <select type="text" name="Odberatel[mesto]" class="form-control" id="odberatel-town">
            <option value=""></option>
            <?php
            foreach($mesto as $it) {
                $selected = '';
                if (!empty($odberatelData) && $odberatelData['mesto'] == $it['nazov_obce']) {
                    $selected = ' selected';
                }
                $jsonIt = json_encode($it);
                echo "<option value='$jsonIt'$selected>{$it['nazov_obce']} ({$it['nazov_okresu']})</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-zip">PSČ:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[psc]" class="form-control" id="odberatel-zip" value="<?= $odberatelData['psc'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="oberatel-country">Štát:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[stat]" class="form-control" id="oberatel-country" value="<?= $odberatelData['stat'] ?? '' ?>">
    </div>
</div>

<br>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-ico">IČO:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[ico]" class="form-control" id="odberatel-ico" value="<?= $odberatelData['ico'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-dic">DIČ:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[dic]" class="form-control" id="odberatel-dic" value="<?= $odberatelData['dic'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="odberatel-icdph">IČ DPH:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[icdph]" class="form-control" id="odberatel-icdph" value="<?= $odberatelData['icdph'] ?? '' ?>">
    </div>
</div>

<br>
<br>

<div class="form-group row" >
    <label class="col-3 col-form-label">Info o odberateľovi:</label>
    <div class="col-9">
        <textarea class="form-control" name="Odberatel[poznamka]"><?= $odberatelData['poznamka'] ?? '' ?></textarea>
    </div>
</div>

<br>

<h6>Adresa dodania (ak je iná ako adresa odberateľa)</h6>
<br>
<div class="form-group row">
    <label class="col-3 col-form-label" for="doda-nazov">Spoločnosť:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[dodacia_nazov]" class="form-control" value="<?= $odberatelData['dodacia_nazov'] ?? '' ?>" id="doda-nazov">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="doda-osoba">Osoba:</label>
    <div class="col-9">
        <input type="text" id="doda-osoba" name="Odberatel[dodacia_kontaktna_osoba]" class="form-control" value="<?= $odberatelData['dodacia_kontaktna_osoba'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="doda-ulica">Ulica:</label>
    <div class="col-9">
        <input type="text" id="doda-ulica" name="Odberatel[dodacia_ulica]" class="form-control" value="<?= $odberatelData['dodacia_ulica'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="dodacia-town">Mesto:</label>
    <div class="col-9">
        <select name="Odberatel[dodacia_mesto]" class="form-control" id="dodacia-town">
            <option value=""></option>
            <?php
            foreach($mesto as $it){
                $selected = '';
                if (!empty($odberatelData) && $odberatelData['dodacia_mesto'] == $it['nazov_obce']) {
                    $selected = ' selected';
                }
                $jsonIt = json_encode($it);
                echo "<option value='$jsonIt'$selected>{$it['nazov_obce']} ({$it['nazov_okresu']})</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label" for="dodacia-zip">PSČ:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[dodacia_psc]" class="form-control" id="dodacia-zip" value="<?= $odberatelData['dodacia_psc'] ?? '' ?>">
    </div>
</div>

<div class="form-group row">
    <label for="dodacia-country" class="col-3 col-form-label">Štát:</label>
    <div class="col-9">
        <input type="text" name="Odberatel[dodacia_stat]" class="form-control" id="dodacia-country" value="<?= $odberatelData['dodacia_stat'] ?? '' ?>">
    </div>
</div>
