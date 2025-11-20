<?php

use backend\assets\RealAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $systemCalendars \common\models\Calendar[] */
/* @var $users \common\models\User[] */
/* @var $personalCalendar \common\models\Calendar */
/* @var $companies \common\models\MyCompanies[] */

$this->title = "Môj kalendár";

$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
    ['depends' => [RealAsset::class]]
);

$eventsUrl  = Url::to(['calendar/events']);
$createUrl  = Url::to(['calendar/create']);
$deleteUrl  = Url::to(['calendar/delete']);

?>

<div class="container-fluid">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <!-- LEFT SIDEBAR -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p style="margin:0 0 5px 0; padding:0;font-weight: 500">Moje kalendáre</p>

                    <button id="btn-generate-plan" class="btn btn-secondary">
                    Môj plán
                    </button>


                    <ul style="padding: 5px; list-style-type: none; margin:0;">
                        <li>
                            <input type="checkbox"
                                   class="calendar-filter"
                                   data-calendar-id="<?= $personalCalendar->id ?>"
                                   checked>
                            <?= htmlspecialchars($personalCalendar->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </li>
                    </ul>

                    <p style="margin:10px 0 5px 0; padding:0;font-weight: 500">Ďalšie kalendáre</p>
                    <ul style="padding: 5px; list-style-type: none; margin:0;">
                        <?php foreach ($systemCalendars as $calendar) : ?>
                            <li>
                                <input type="checkbox"
                                       class="calendar-filter"
                                       data-calendar-id="<?= $calendar->id ?>"
                                       checked>
                                <?= htmlspecialchars($calendar->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- MAIN CALENDAR -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL: create / basic edit event -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="eventForm">
        <div class="modal-header">
          <h5 class="modal-title">Nová udalosť</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Zavrieť">
            <span aria-hidden="true">&times;</span>
          </button>
          
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label>Názov</label>
            <input type="text" name="title" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Typ</label>
            <select name="type" class="form-control">
              <option value="workday">Pracovný deň</option>
              <option value="shift">Zmena</option>
              <option value="doctor">Doktor</option>
              <option value="sick">PN</option>
              <option value="holiday">Sviatok</option>
              <option value="other">Iné</option>
            </select>
          </div>

          <div class="form-group">
            <label>Kalendár</label>
            <select name="calendar_id" class="form-control">
              <option value="<?= $personalCalendar->id ?>">
                  <?= htmlspecialchars($personalCalendar->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
              </option>
              <?php foreach ($systemCalendars as $calendar) : ?>
                  <option value="<?= $calendar->id ?>">
                      <?= htmlspecialchars($calendar->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                  </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Začiatok</label>
            <input type="datetime-local" name="start" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Koniec</label>
            <input type="datetime-local" name="end" class="form-control">
          </div>

          <div class="form-group">
            <label>Celý deň</label>
            <input type="checkbox" name="all_day" value="1">
          </div>

          <div class="form-group">
            <label>Miesto</label>
            <input type="text" name="location" class="form-control">
          </div>

          <!-- COMPANY -->
          <div class="form-group">
            <label>Spoločnosť</label>
            <select name="company" class="form-control">
                <option value="">-- Bez spoločnosti --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= htmlspecialchars($company->company_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <?= htmlspecialchars($company->company_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
          </div>

          <!-- CONTACT -->
          <div class="form-group">
            <label>Kontakt</label>
            <input type="text" name="contact" class="form-control"
                   placeholder="napr. 0903 123 456 alebo meno + číslo">
          </div>

          <!-- supervisors as CHECKBOX LIST + SEARCH -->
          <div class="form-group">
            <label>Supervízori</label>
            <input type="text"
                   class="form-control mb-1 ao-user-filter"
                   placeholder="Hľadať supervízora..."
                   data-target="#supervisors-list">
            <div class="ao-checkbox-list" id="supervisors-list">
                <?php foreach ($users as $user): ?>
                    <?php $label = $user->username ?: $user->email ?: ('ID ' . $user->id); ?>
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               name="supervisors[]"
                               value="<?= $user->id ?>"
                               id="supervisor-<?= $user->id ?>">
                        <label class="form-check-label" for="supervisor-<?= $user->id ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small class="form-text text-muted">
                Zaškrtni jedného alebo viacerých supervízorov.
            </small>
          </div>

          <!-- teammates as CHECKBOX LIST + SEARCH -->
          <div class="form-group">
            <label>Členovia tímu</label>
            <input type="text"
                   class="form-control mb-1 ao-user-filter"
                   placeholder="Hľadať člena tímu..."
                   data-target="#teammates-list">
            <div class="ao-checkbox-list" id="teammates-list">
                <?php foreach ($users as $user): ?>
                    <?php $label = $user->username ?: $user->email ?: ('ID ' . $user->id); ?>
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               name="teammates[]"
                               value="<?= $user->id ?>"
                               id="teammate-<?= $user->id ?>">
                        <label class="form-check-label" for="teammate-<?= $user->id ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small class="form-text text-muted">
                Zaškrtni členov tímu, ktorí budú pracovať na tejto úlohe.
            </small>
          </div>

          <div class="form-group">
            <label>Nástroje / pracovné pomôcky</label>
            <input type="text" name="tools" class="form-control"
                   placeholder="napr. rebrík, náradie, notebook...">
          </div>

          <div class="form-group">
            <label>Vozidlo / doprava</label>
            <input type="text" name="vehicles" class="form-control"
                   placeholder="napr. Firemné auto 1, dodávka...">
          </div>

          <div class="form-group">
            <label>Poznámka</label>
            <textarea name="notes" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-primary">Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: event detail card -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detail-title">Udalosť</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Zavrieť">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Dátum &amp; čas:</strong><br><span id="detail-datetime"></span></p>
        <p><strong>Miesto:</strong><br><span id="detail-location"></span></p>
        <p><strong>Spoločnosť:</strong><br><span id="detail-company"></span></p>
        <p><strong>Kontakt:</strong><br><span id="detail-contact"></span></p>
        <p><strong>Supervízori:</strong><br><span id="detail-supervisors"></span></p>
        <p><strong>Členovia tímu:</strong><br><span id="detail-teammates"></span></p>
        <p><strong>Nástroje / pomôcky:</strong><br><span id="detail-tools"></span></p>
        <p><strong>Vozidlá / doprava:</strong><br><span id="detail-vehicles"></span></p>
        <p><strong>Poznámka:</strong><br><span id="detail-notes"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger mr-auto" id="detail-delete-btn">Vymazať</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavrieť</button>
      </div>
    </div>
  </div>
</div>

<?php
$css = <<<CSS
.fc-daygrid-day-number {
    font-size: 1.0em !important;
    color: #000 !important;
}
.fc-day {
    cursor: pointer;
}

/* ===== RESPONSIVE CALENDAR MODAL ===== */

#eventModal .modal-dialog {
    max-width: 650px;
    margin: 20px auto;
}

#eventModal .modal-content {
    display: flex;
    flex-direction: column;
}

#eventModal .modal-body {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

@media (max-width: 576px) {
    #eventModal .modal-dialog {
        width: 100%;
        max-width: 100%;
        margin: 0;
    }

    #eventModal .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    #eventModal .modal-body {
        max-height: calc(100vh - 140px);
    }
}

/* checkbox lists for supervisors/teammates */
.ao-checkbox-list {
    max-height: 180px;
    overflow-y: auto;
    padding: 6px 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background: #fafafa;
}

/* ===== SIMPLE STACKED EVENT CONTENT ===== */

.fc-daygrid-event {
    white-space: normal;
    padding: 2px 3px;
}

.fc-daygrid-event-harness {
    height: auto !important;
}

.ao-event {
    display: block;
    text-align: center;
}

.ao-event-line {
    font-size: 0.72rem;
    line-height: 1.2;
}

.ao-event-title {
    font-weight: 600;
}

.ao-event-time {
    font-weight: 600;
}

.ao-event-location {
    font-style: italic;
}

.ao-event-super {
    opacity: 0.95;
}

/* ===== COLORS BY TYPE ===== */
.fc-daygrid-event.ao-type-workday {
    background-color: #ffffff !important;
    border-color: #ced4da !important;
    color: #212529 !important;
}
.fc-daygrid-event.ao-type-workday .ao-event-line {
    color: #212529 !important;
}

.fc-daygrid-event.ao-type-shift {
    background-color: #f8d7da !important;
    border-color: #f5c6cb !important;
    color: #721c24 !important;
}
.fc-daygrid-event.ao-type-shift .ao-event-line {
    color: #721c24 !important;
}

.fc-daygrid-event.ao-type-doctor {
    background-color: #cce5ff !important;
    border-color: #b8daff !important;
    color: #004085 !important;
}
.fc-daygrid-event.ao-type-doctor .ao-event-line {
    color: #004085 !important;
}

.fc-daygrid-event.ao-type-sick {
    background-color: #d4edda !important;
    border-color: #c3e6cb !important;
    color: #155724 !important;
}
.fc-daygrid-event.ao-type-sick .ao-event-line {
    color: #155724 !important;
}

.fc-daygrid-event.ao-type-holiday {
    background-color: #d1ecf1 !important;
    border-color: #bee5eb !important;
    color: #0c5460 !important;
}
.fc-daygrid-event.ao-type-holiday .ao-event-line {
    color: #0c5460 !important;
}

.fc-daygrid-event.ao-type-other {
    background-color: #ffe5b4 !important;
    border-color: #ffd59b !important;
    color: #856404 !important;
}
.fc-daygrid-event.ao-type-other .ao-event-line {
    color: #856404 !important;
}
CSS;
$this->registerCss($css);
?>

<script>
    const eventsUrl      = '<?= $eventsUrl ?>';
    const createUrl      = '<?= $createUrl ?>';
    const deleteUrl      = '<?= $deleteUrl ?>';
    const generatePlanUrl = '<?= \yii\helpers\Url::to(['calendar/generate-work-plan']) ?>';
    const csrfToken      = '<?= Yii::$app->request->csrfToken ?>';

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // filter checkboxes in user list based on search input
    function initUserFilters() {
        document.querySelectorAll('.ao-user-filter').forEach(function (input) {
            input.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                const targetSelector = this.getAttribute('data-target');
                if (!targetSelector) return;
                const container = document.querySelector(targetSelector);
                if (!container) return;

                container.querySelectorAll('.form-check').forEach(function (row) {
                    const labelText = row.textContent.toLowerCase();
                    row.style.display = labelText.indexOf(query) !== -1 ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        let currentEventId = null;
        let currentEventCanDelete = false;

        function getSelectedCalendarIds() {
            const checked = document.querySelectorAll('.calendar-filter:checked');
            return Array.from(checked).map(cb => cb.dataset.calendarId);
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'sk',
            headerToolbar: {
                left: 'prev today next pushEvent',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Dnes',
                month: 'Mesiac',
                week: 'Týždeň',
                day: 'Deň'
            },
            customButtons: {
                pushEvent: {
                    text: 'Pridať udalosť',
                    click: function() {
                        resetEventForm();
                        const now = new Date();
                        const yyyy = now.getFullYear();
                        const mm = String(now.getMonth() + 1).padStart(2, '0');
                        const dd = String(now.getDate()).padStart(2, '0');
                        const startInput = document.querySelector('#eventForm [name="start"]');
                        startInput.value = yyyy + '-' + mm + '-' + dd + 'T08:00';
                        $('#eventModal').modal('show');
                    }
                }
            },
            firstDay: 1,
            slotMinTime: '07:00:00',
            slotMaxTime: '20:00:00',
            selectable: true,

            eventClassNames: function(arg) {
                const ext = arg.event.extendedProps || {};
                const t   = ext.type || '';
                if (!t) return [];
                return ['ao-type-' + t];
            },

            eventContent: function(arg) {
                const event = arg.event;
                const ext   = event.extendedProps || {};

                let timeText = '';
                const start = event.start;
                const end   = event.end;

                if (start) {
                    if (event.allDay) {
                        timeText = 'Celý deň';
                    } else {
                        const tOpts = { hour: '2-digit', minute: '2-digit' };
                        timeText = start.toLocaleTimeString('sk-SK', tOpts);
                        if (end) {
                            if (end.toDateString() === start.toDateString()) {
                                timeText += ' – ' + end.toLocaleTimeString('sk-SK', tOpts);
                            } else {
                                const endDateTime = end.toLocaleString('sk-SK', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                timeText += ' → ' + endDateTime;
                            }
                        }
                    }
                }

                let html = '<div class="ao-event">';

                html += '<div class="ao-event-line ao-event-title">' +
                        escHtml(event.title || '') +
                        '</div>';

                if (timeText) {
                    html += '<div class="ao-event-line ao-event-time">' +
                            escHtml(timeText) +
                            '</div>';
                }

                if (ext.location) {
                    html += '<div class="ao-event-line ao-event-location">' +
                            escHtml(ext.location) +
                            '</div>';
                }

                if (ext.supervisors_label) {
                    html += '<div class="ao-event-line ao-event-super">' +
                            'Supervízor: ' + escHtml(ext.supervisors_label) +
                            '</div>';
                }

                html += '</div>';

                return { html: html };
            },

            events: function(info, successCallback, failureCallback) {
                const params = new URLSearchParams();
                params.append('start', info.startStr);
                params.append('end', info.endStr);

                const ids = getSelectedCalendarIds();
                if (ids.length) {
                    params.append('calendars', ids.join(','));
                }

                fetch(eventsUrl + '?' + params.toString())
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        successCallback(data);
                    })
                    .catch(function(err) {
                        console.error(err);
                        failureCallback(err);
                    });
            },

            dateClick: function(info) {
                resetEventForm();
                const startInput = document.querySelector('#eventForm [name="start"]');
                startInput.value = info.dateStr + 'T08:00';
                $('#eventModal').modal('show');
            },

            eventClick: function(info) {
                const event = info.event;
                const ext   = event.extendedProps || {};

                currentEventId = event.id;
                currentEventCanDelete = !!ext.canDelete;

                const deleteBtn = document.getElementById('detail-delete-btn');
                deleteBtn.style.display = currentEventCanDelete ? 'inline-block' : 'none';

                document.getElementById('detail-title').textContent = event.title || 'Udalosť';

                let dateText = '';
                const start = event.start;
                const end   = event.end;

                if (start) {
                    const baseDateOpts = { day: '2-digit', month: '2-digit', year: 'numeric' };
                    const timeOpts     = { hour: '2-digit', minute: '2-digit' };

                    if (event.allDay) {
                        dateText = start.toLocaleDateString('sk-SK', baseDateOpts);
                        if (end && end.toDateString() !== start.toDateString()) {
                            dateText += ' – ' + end.toLocaleDateString('sk-SK', baseDateOpts);
                        } else {
                            dateText += ' (celý deň)';
                        }
                    } else {
                        dateText = start.toLocaleDateString('sk-SK', baseDateOpts) +
                                   ' ' + start.toLocaleTimeString('sk-SK', timeOpts);
                        if (end) {
                            dateText += ' – ';
                            if (end.toDateString() !== start.toDateString()) {
                                dateText += end.toLocaleDateString('sk-SK', baseDateOpts) + ' ';
                            }
                            dateText += end.toLocaleTimeString('sk-SK', timeOpts);
                        }
                    }
                }

                document.getElementById('detail-datetime').textContent    = dateText || '-';
                document.getElementById('detail-location').textContent    = ext.location || '-';
                document.getElementById('detail-company').textContent     = ext.company || '-';
                document.getElementById('detail-contact').textContent     = ext.contact || '-';
                document.getElementById('detail-supervisors').textContent = ext.supervisors_label || '-';
                document.getElementById('detail-teammates').textContent   = ext.teammates_label || '-';
                document.getElementById('detail-tools').textContent       = ext.tools || '-';
                document.getElementById('detail-vehicles').textContent    = ext.vehicles || '-';
                document.getElementById('detail-notes').textContent       = ext.notes || '-';

                $('#eventDetailModal').modal('show');
            }
        });

        calendar.render();

        document.querySelectorAll('.calendar-filter').forEach(function(cb) {
            cb.addEventListener('change', function() {
                calendar.refetchEvents();
            });
        });

        initUserFilters();

        function resetEventForm() {
            const form = document.getElementById('eventForm');
            form.reset();

            const allDayCheckbox = form.querySelector('[name="all_day"]');
            if (allDayCheckbox) {
                allDayCheckbox.checked = false;
            }

            // clear search filters & show all rows again
            document.querySelectorAll('.ao-user-filter').forEach(function (input) {
                input.value = '';
            });
            document.querySelectorAll('.ao-checkbox-list .form-check').forEach(function (row) {
                row.style.display = '';
            });
        }

        // create / save event
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            if (!form.querySelector('[name="all_day"]').checked) {
                formData.set('all_day', '0');
            }

            fetch(createUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken
                }
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        $('#eventModal').modal('hide');
                        calendar.refetchEvents();
                    } else {
                        console.error(data);
                        let msg = 'Chyba pri ukladaní udalosti';
                        if (data.message) {
                            msg += ': ' + data.message;
                        } else if (data.errors) {
                            msg += ' - ' + JSON.stringify(data.errors);
                        }
                        alert(msg);
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Chyba pri ukladaní udalosti: ' + err);
                });
        });

        // delete from detail card
        document.getElementById('detail-delete-btn').addEventListener('click', function() {
            if (!currentEventId || !currentEventCanDelete) {
                return;
            }
            if (!confirm('Naozaj chceš odstrániť túto udalosť?')) {
                return;
            }

            const fd = new FormData();
            fd.append('id', currentEventId);

            fetch(deleteUrl, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken
                }
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        $('#eventDetailModal').modal('hide');
                        calendar.refetchEvents();
                    } else {
                        console.error(data);
                        alert(data.message || 'Udalosť sa nepodarilo vymazať.');
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Chyba pri mazaní udalosti.');
                });
        });

        // generate work-plan button (if present)
        const btnGeneratePlan = document.getElementById('btn-generate-plan');
        if (btnGeneratePlan) {
            btnGeneratePlan.addEventListener('click', function () {
                if (!confirm('Naozaj vygenerovať pracovný plán do kalendára?')) {
                    return;
                }

                fetch(generatePlanUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': csrfToken
                    }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            let msg = 'Plán vygenerovaný.';
                            if (typeof data.createdEvents !== 'undefined') {
                                msg += ' Vytvorených udalostí: ' + data.createdEvents + '.';
                            }
                            alert(msg);
                            calendar.refetchEvents();
                        } else {
                            console.error(data);
                            alert(data.message || 'Nepodarilo sa vygenerovať plán.');
                        }
                    })
                    .catch(function (err) {
                        console.error(err);
                        alert('Chyba pri generovaní plánu.');
                    });
            });
        }
    });
</script>
