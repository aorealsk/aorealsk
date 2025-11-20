<?php
/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'Dochádzka & Súbory';

$startUrl      = Url::to(['user-attendance/start']);
$endUrl        = Url::to(['user-attendance/end']);
$uploadUrl     = Url::to(['user-attendance/upload']);
$listUrl       = Url::to(['user-attendance/list-today']);
$listOlderUrl  = Url::to(['user-attendance/list-older']);
$saveNoteUrl   = Url::to(['user-attendance/save-comment']);

$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$forcedUid = isset($userId) ? (int)$userId : null;
?>
<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:0;padding:0px;background:#f6f7fb}
    .wrap{max-width:1100px;margin:0 auto}
    h1{font-size:22px;margin-bottom:10px}
    .card{background:#fff;border-radius:12px;padding:16px;margin:12px 0;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .row{display:flex;gap:16px;flex-wrap:wrap}
    .col{flex:1 1 320px}
    button{cursor:pointer;border:0;border-radius:10px;padding:12px 16px;font-weight:600}
    .start{background:#16a34a;color:#fff}
    .end{background:#dc2626;color:#fff}
    .muted{opacity:.8}
    #video{width:100%;border-radius:10px;background:#000}
    #uploads{border:2px dashed #cbd5e1;border-radius:10px;padding:16px;text-align:center}
    #uploads.drag{background:#f0f9ff;border-color:#38bdf8}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px 10px;border-bottom:1px solid #eee;font-size:14px;vertical-align:top}
    .note{display:flex;gap:8px}
    input[type=text]{flex:1;padding:10px;border:1px solid #e5e7eb;border-radius:8px}
    .right{display:flex;justify-content:flex-end}
    .busy{display:none;margin-left:8px}
    .busy.show{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#475569}
    .totals{display:none}
    .totals dl{display:grid;grid-template-columns:auto 1fr;gap:6px 12px;margin:0}
    .totals dt{color:#475569}
    .totals dd{margin:0;font-weight:600}
  </style>
</head>
<body>
<div class="wrap">
  <h1>Dashboard: Dochádzka & Súbory</h1>

  <div class="card row">
    <div class="col">
      <video id="video" autoplay playsinline></video>
      <canvas id="canvas" style="display:none"></canvas>
      <div style="display:flex;gap:10px;margin-top:10px;align-items:center">
        <button id="btnStart" class="start">Začiatok práce (selfie)</button>
        <button id="btnEnd" class="end" disabled>Koniec práce (selfie)</button>
        <span id="busy" class="busy">
          <svg width="14" height="14" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="#94a3b8" stroke-width="3" fill="none" opacity=".3"/><path d="M22 12a10 10 0 0 1-10 10" stroke="#94a3b8" stroke-width="3" fill="none"></path></svg>
          Ukladám...
        </span>
      </div>
      <p class="muted">Povolenie kamery je povinné pri štarte/konci zmeny.</p>
    </div>
    <div class="col">
      <div id="uploads">
        <p><strong>Nahraj fotky &amp; dokumenty</strong></p>
        <p>Pretiahni sem súbory alebo klikni</p>
        <input id="fileInput" type="file" multiple style="display:none"
               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
        <button id="pickBtn">Vybrať súbory</button>
      </div>
      <div style="margin-top:12px" class="note">
        <input id="note" type="text" placeholder="Poznámka k zmene (nepovinné)" />
        <button id="saveNote">Uložiť</button>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Dnešné zmeny</h2>
    <table id="tblToday"><thead>
      <tr><th>ID</th><th>Začiatok</th><th>Koniec</th><th>Odpracované</th><th>Start selfie</th><th>End selfie</th><th>Poznámka</th></tr>
    </thead><tbody></tbody></table>
  </div>

  <div class="card totals" id="totalsCard">
    <h2>Súhrn</h2>
    <dl>
      <dt>Dnes:</dt><dd id="totDay">–</dd>
      <dt>Aktuálny mesiac:</dt><dd id="totMonth">–</dd>
      <dt>Aktuálny rok:</dt><dd id="totYear">–</dd>
    </dl>
  </div>

  <div class="card">
    <h2>Všetky zmeny</h2>
    <div id="olderContainer">
      <table><thead>
        <tr><th>Dátum</th><th>Príchod</th><th>Odchod</th><th>Poznámka / Detaily</th></tr>
      </thead><tbody id="olderBody"></tbody></table>
      <p id="olderEmpty" class="muted" style="display:none;margin-top:8px">Žiadne zmeny.</p>
    </div>
  </div>
</div>

<script>
(() => {
  const START_URL      = '<?= $startUrl ?>';
  const END_URL        = '<?= $endUrl ?>';
  const UPLOAD_URL     = '<?= $uploadUrl ?>';
  const LIST_URL       = '<?= $listUrl ?>';
  const LIST_OLDER_URL = '<?= $listOlderUrl ?>';
  const SAVE_NOTE_URL  = '<?= $saveNoteUrl ?>';

  const CSRF_PARAM = '<?= $csrfParam ?>';
  const CSRF_TOKEN = '<?= $csrfToken ?>';
  const FORCED_UID = <?= $forcedUid !== null ? (int)$forcedUid : 'null' ?>;

  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const btnStart = document.getElementById('btnStart');
  const btnEnd = document.getElementById('btnEnd');
  const busy = document.getElementById('busy');
  const tblTodayBody = document.querySelector('#tblToday tbody');
  const olderBody = document.getElementById('olderBody');
  const olderEmpty= document.getElementById('olderEmpty');
  const note = document.getElementById('note');
  const saveNote = document.getElementById('saveNote');

  // ---------- Camera ----------
  navigator.mediaDevices.getUserMedia({ video:true, audio:false })
    .then(s => video.srcObject = s)
    .catch(e => alert('Kamera je potrebná na selfie: ' + e.message));

  function captureDataURL(){
    const w = video.videoWidth || 640, h = video.videoHeight || 480;
    canvas.width = w; canvas.height = h;
    canvas.getContext('2d').drawImage(video, 0, 0, w, h);
    return canvas.toDataURL('image/jpeg', 0.9);
  }

  // ---------- Helpers ----------
  function addCsrf(body){
    if (body instanceof FormData) {
      if (!body.has(CSRF_PARAM)) body.append(CSRF_PARAM, CSRF_TOKEN);
    } else {
      const p = new URLSearchParams(body || {});
      if (!p.has(CSRF_PARAM)) p.append(CSRF_PARAM, CSRF_TOKEN);
      body = p;
    }
    return body;
  }

  async function post(url, body){
    body = addCsrf(body);
    const res = await fetch(url, { method:'POST', headers:{'Accept':'application/json'}, credentials:'same-origin', body });
    const ct = res.headers.get('content-type') || '';
    if (!ct.includes('application/json')) throw new Error('Server nevrátil JSON ('+res.status+').');
    return res.json();
  }

  function setBusy(on){
    busy.classList.toggle('show', !!on);
    btnStart.disabled = on || btnStart.disabled;
    btnEnd.disabled   = on || btnEnd.disabled;
  }

  function setButtons(started, ended){
    btnStart.disabled = !!started;
    btnEnd.disabled = !(started && !ended);
  }

  function updateTotals(json){
    if (!json) return;
    const totDay=document.getElementById('totDay'), totMonth=document.getElementById('totMonth'), totYear=document.getElementById('totYear'), totalsCard=document.getElementById('totalsCard');
    let visible=false;
    if (json.day_total_time){ totDay.textContent=json.day_total_time; visible=true; }
    if (json.month_total_time){ totMonth.textContent=json.month_total_time; visible=true; }
    if (json.year_total_time){ totYear.textContent=json.year_total_time; visible=true; }
    totalsCard.style.display = visible ? 'block' : 'none';
  }

  async function refreshToday(){
    try{
      const data = {};
      if (FORCED_UID) data.uid = FORCED_UID;
      const json = await post(LIST_URL, data);
      tblTodayBody.innerHTML = '';
      (json.shifts || []).forEach(s => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${s.id ?? ''}</td>
          <td>${s.started_at ?? ''}</td>
          <td>${s.ended_at ?? ''}</td>
          <td>${s.duration_human ?? ''}</td>
          <td>${s.start_photo ? `<a href="${s.start_photo}" target="_blank">zobraziť</a>` : ''}</td>
          <td>${s.end_photo ? `<a href="${s.end_photo}" target="_blank">zobraziť</a>` : ''}</td>
          <td>${s.note ?? ''}</td>`;
        tblTodayBody.appendChild(tr);
      });
      const s=(json.shifts&&json.shifts[0])||null;
      setButtons(!!(s&&s.started_at),!!(s&&s.ended_at));
      updateTotals(json);
    }catch(e){ setButtons(false,true); }
  }

  // ---------- Load ALL shifts ----------
  async function loadAllShifts(){
    try {
      const data = { limit: 999999, offset: 0 }; // fetch everything
      if (FORCED_UID) data.uid = FORCED_UID;
      const json = await post(LIST_OLDER_URL, data);
      if (!json.ok) throw new Error(json.error || 'Načítanie zlyhalo');
      olderBody.innerHTML = '';
      if (json.html && json.html.trim()){
        const tmp = document.createElement('tbody');
        tmp.innerHTML = json.html;
        [...tmp.children].forEach(tr => olderBody.appendChild(tr));
        olderEmpty.style.display = 'none';
      } else olderEmpty.style.display = 'block';
    } catch(e){
      olderBody.innerHTML = '';
      olderEmpty.style.display = 'block';
      console.error(e);
    }
  }

  // ---------- Start & End ----------
  btnStart.onclick = async () => {
    if (!confirm('Naozaj sa chcete prihlásiť na začiatok zmeny?')) return;
    try{
      const img=captureDataURL();
      if(!img){alert('Selfie sa nepodarilo odfotiť. Skontrolujte kameru.');return;}
      setBusy(true);
      const body={image:img};
      if(FORCED_UID)body.uid=FORCED_UID;
      const j=await post(START_URL,body);
      if(j.ok||j.status==='ok'){setButtons(true,false);alert('Úspešne ste sa prihlásili na zmenu.');}
      else alert(j.error||'Začiatok zmeny zlyhal.');
    }catch(e){alert(e.message||'Začiatok zmeny zlyhal.');}
    finally{setBusy(false);refreshToday();loadAllShifts();}
  };

  btnEnd.onclick = async () => {
    if (!confirm('Naozaj sa chcete odhlásiť z konca zmeny?')) return;
    try{
      const img=captureDataURL();
      if(!img){alert('Selfie sa nepodarilo odfotiť. Skontrolujte kameru.');return;}
      setBusy(true);
      const body={image:img};
      if(FORCED_UID)body.uid=FORCED_UID;
      const j=await post(END_URL,body);
      if(j.ok||j.status==='ok'){setButtons(true,true);alert('Úspešne ste sa odhlásili zo zmeny.');updateTotals(j);}
      else alert(j.error||'Koniec zmeny zlyhal.');
    }catch(e){alert(e.message||'Koniec zmeny zlyhal.');}
    finally{setBusy(false);refreshToday();loadAllShifts();}
  };

  // ---------- Init ----------
  refreshToday();
  loadAllShifts();
})();
</script>

</body>
</html>
