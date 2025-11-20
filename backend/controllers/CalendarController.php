<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\Calendar;
use common\models\CalendarEvent;
use common\models\User;
use common\models\MyCompanies;
use common\models\CalendarHoliday;
use common\models\CalendarNameday;
use common\models\StudyPlanItem;
use common\models\UserAttendance; // still used for hasAttendance()

class CalendarController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        // osobný kalendár pre aktuálneho používateľa (nájdi alebo vytvor)
        $personalCalendar = Calendar::findOne([
            'type'    => 'user',
            'user_id' => $userId,
        ]);

        if ($personalCalendar === null) {
            $personalCalendar = new Calendar();
            $personalCalendar->title      = 'Môj kalendár';
            $personalCalendar->type       = 'user';
            $personalCalendar->user_id    = $userId;
            $personalCalendar->color      = '#42a5f5';
            $personalCalendar->created_at = time();
            $personalCalendar->updated_at = time();
            $personalCalendar->save(false);
        }

        // systémové kalendáre pre sviatky / pamätné dni / meniny (ak neexistujú, vytvor)
        $holidayCalendar = Calendar::findOne(['type' => 'holiday', 'title' => 'Štátne sviatky']);
        if ($holidayCalendar === null) {
            $holidayCalendar = new Calendar();
            $holidayCalendar->title      = 'Štátne sviatky';
            $holidayCalendar->type       = 'holiday';
            $holidayCalendar->user_id    = null;
            $holidayCalendar->color      = '#ffcdd2'; // svetlo červená
            $holidayCalendar->created_at = time();
            $holidayCalendar->updated_at = time();
            $holidayCalendar->save(false);
        }

        $memorialCalendar = Calendar::findOne(['type' => 'holiday', 'title' => 'Pamätné dni']);
        if ($memorialCalendar === null) {
            $memorialCalendar = new Calendar();
            $memorialCalendar->title      = 'Pamätné dni';
            $memorialCalendar->type       = 'holiday';
            $memorialCalendar->user_id    = null;
            $memorialCalendar->color      = '#e0e0e0'; // sivá
            $memorialCalendar->created_at = time();
            $memorialCalendar->updated_at = time();
            $memorialCalendar->save(false);
        }

        $namedayCalendar = Calendar::findOne(['type' => 'holiday', 'title' => 'Meniny']);
        if ($namedayCalendar === null) {
            $namedayCalendar = new Calendar();
            $namedayCalendar->title      = 'Meniny';
            $namedayCalendar->type       = 'holiday';
            $namedayCalendar->user_id    = null;
            $namedayCalendar->color      = '#d1c4e9'; // svetlo fialová
            $namedayCalendar->created_at = time();
            $namedayCalendar->updated_at = time();
            $namedayCalendar->updated_at = time();
            $namedayCalendar->save(false);
        }

        // systémové / holiday kalendáre pre ľavý panel
        $systemCalendars = Calendar::find()
            ->where(['type' => ['system', 'holiday']])
            ->orderBy(['title' => SORT_ASC])
            ->all();

        // všetci useri pre supervisors / teammates
        $users = User::find()
            ->orderBy(['username' => SORT_ASC])
            ->all();

        // firmy pre select
        $companies = MyCompanies::find()
            ->orderBy(['company_name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'systemCalendars'  => $systemCalendars,
            'users'            => $users,
            'personalCalendar' => $personalCalendar,
            'companies'        => $companies,
        ]);
    }

    /**
     * JSON feed pre FullCalendar.
     */
    public function actionEvents($start = null, $end = null, $calendars = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->user->id ?? null;

        // zoznam kalendárov z check-boxov
        $calendarIdsFilter = null;
        if (!empty($calendars)) {
            $calendarIdsFilter = array_map('intval', explode(',', $calendars));
        }

        // 1) bežné udalosti z calendar_event
        $query = CalendarEvent::find();

        if ($start && $end) {
            $query->andWhere(['between', 'start', $start, $end]);
        }

        if ($calendarIdsFilter) {
            $query->andWhere(['calendar_id' => $calendarIdsFilter]);
        }

        if ($userId) {
            $query->andWhere([
                'or',
                ['user_id' => $userId],
                ['user_id' => null], // systémové / holiday udalosti
            ]);
        }

        $models = $query->all();

        // nazbieraj všetky user_id zo supervisors / teammates
        $userIdSet = [];
        foreach ($models as $event) {
            if ($event->supervisors) {
                foreach (explode(',', $event->supervisors) as $id) {
                    $id = (int)trim($id);
                    if ($id) {
                        $userIdSet[$id] = true;
                    }
                }
            }
            if ($event->teammates) {
                foreach (explode(',', $event->teammates) as $id) {
                    $id = (int)trim($id);
                    if ($id) {
                        $userIdSet[$id] = true;
                    }
                }
            }
        }

        $userMap = [];
        if (!empty($userIdSet)) {
            $userMap = User::find()
                ->select(['id', 'username', 'email'])
                ->where(['id' => array_keys($userIdSet)])
                ->indexBy('id')
                ->asArray()
                ->all();
        }

        // farby podľa typu
        $typeColors = [
            'workday' => [
                'bg'    => '#ffffff',
                'border'=> '#ced4da',
                'text'  => '#212529',
            ],
            'shift' => [
                'bg'    => '#f8d7da',
                'border'=> '#f5c6cb',
                'text'  => '#721c24',
            ],
            'doctor' => [
                'bg'    => '#cce5ff',
                'border'=> '#b8daff',
                'text'  => '#004085',
            ],
            'sick' => [
                'bg'    => '#d4edda',
                'border'=> '#c3e6cb',
                'text'  => '#155724',
            ],
            'holiday' => [
                'bg'    => '#d1ecf1',
                'border'=> '#bee5eb',
                'text'  => '#0c5460',
            ],
            'other' => [
                'bg'    => '#ffe5b4',
                'border'=> '#ffd59b',
                'text'  => '#856404',
            ],
        ];

        $events = [];

        foreach ($models as $event) {
            $calendar = $event->calendar;

            // supervízori
            $supervisorNames = [];
            if ($event->supervisors) {
                foreach (explode(',', $event->supervisors) as $id) {
                    $id = (int)trim($id);
                    if ($id && isset($userMap[$id])) {
                        $u = $userMap[$id];
                        $label = $u['username'] ?: $u['email'] ?: ('ID ' . $id);
                        $supervisorNames[] = $label;
                    }
                }
            }
            $supervisorsLabel = implode(', ', $supervisorNames);

            // členovia tímu
            $teammateNames = [];
            if ($event->teammates) {
                foreach (explode(',', $event->teammates) as $id) {
                    $id = (int)trim($id);
                    if ($id && isset($userMap[$id])) {
                        $u = $userMap[$id];
                        $label = $u['username'] ?: $u['email'] ?: ('ID ' . $id);
                        $teammateNames[] = $label;
                    }
                }
            }
            $teammatesLabel = implode(', ', $teammateNames);

            // farby
            if (isset($typeColors[$event->type])) {
                $palette     = $typeColors[$event->type];
                $bgColor     = $palette['bg'];
                $borderColor = $palette['border'];
                $textColor   = $palette['text'];
            } else {
                $bgColor     = $calendar ? $calendar->color : '#3a87ad';
                $borderColor = $bgColor;
                $textColor   = '#ffffff';
            }

            // či môže aktuálny user mazať túto udalosť
            $canDelete = $userId && $event->user_id && ((int)$event->user_id === (int)$userId);

            $events[] = [
                'id'              => $event->id,
                'title'           => $event->title,
                'start'           => $event->start,
                'end'             => $event->end,
                'allDay'          => (bool)$event->all_day,
                'backgroundColor' => $bgColor,
                'borderColor'     => $borderColor,
                'textColor'       => $textColor,
                'extendedProps'   => [
                    'location'          => $event->location,
                    'supervisors_label' => $supervisorsLabel,
                    'teammates_label'   => $teammatesLabel,
                    'type'              => $event->type,
                    'company'           => $event->company,
                    'contact'           => $event->contact,
                    'tools'             => $event->tools,
                    'vehicles'          => $event->vehicles,
                    'notes'             => $event->notes,
                    'canDelete'         => $canDelete,
                ],
            ];
        }

        // 2) doplniť systémové sviatky + meniny ako "virtuálne" udalosti
        if ($start && $end) {
            try {
                $startDate = new \DateTime($start);
                $endDate   = new \DateTime($end);
            } catch (\Throwable $e) {
                return $events;
            }

            // FullCalendar posiela end ako exkluzívny -> zoberieme o deň menej
            $endDate->modify('-1 day');

            // nájdeme kalendáre pre sviatky / pamätné dni / meniny
            $holidayCalendar   = Calendar::findOne(['type' => 'holiday', 'title' => 'Štátne sviatky']);
            $memorialCalendar  = Calendar::findOne(['type' => 'holiday', 'title' => 'Pamätné dni']);
            $namedayCalendar   = Calendar::findOne(['type' => 'holiday', 'title' => 'Meniny']);

            // podľa filtrov (checkboxy)
            $wantHolidays  = !$calendarIdsFilter || ($holidayCalendar && in_array($holidayCalendar->id, $calendarIdsFilter));
            $wantMemorials = !$calendarIdsFilter || ($memorialCalendar && in_array($memorialCalendar->id, $calendarIdsFilter));
            $wantNamedays  = !$calendarIdsFilter || ($namedayCalendar && in_array($namedayCalendar->id, $calendarIdsFilter));

            // index sviatkov podľa (month-day)
            $holidayIndex = [];
            if ($wantHolidays || $wantMemorials) {
                foreach (CalendarHoliday::find()->all() as $h) {
                    $key = (int)$h->month . '-' . (int)$h->day;
                    $holidayIndex[$key][] = $h;
                }
            }

            // index menín podľa (month-day)
            $namedayIndex = [];
            if ($wantNamedays) {
                foreach (CalendarNameday::find()->all() as $nd) {
                    $key = (int)$nd->month . '-' . (int)$nd->day;
                    $namedayIndex[$key] = $nd;
                }
            }

            for ($d = clone $startDate; $d <= $endDate; $d->modify('+1 day')) {
                $m   = (int)$d->format('n');
                $day = (int)$d->format('j');
                $key = $m . '-' . $day;
                $dateStr = $d->format('Y-m-d');

                // štátne + pamätné dni
                if (($wantHolidays || $wantMemorials) && isset($holidayIndex[$key])) {
                    foreach ($holidayIndex[$key] as $h) {
                        $isMemorial = ($h->category === 'pamatny');
                        if ($isMemorial && !$wantMemorials) {
                            continue;
                        }
                        if (!$isMemorial && !$wantHolidays) {
                            continue;
                        }

                        $calendarObj = $isMemorial ? $memorialCalendar : $holidayCalendar;
                        if (!$calendarObj) {
                            continue;
                        }

                        $bg = $calendarObj->color ?: ($isMemorial ? '#e0e0e0' : '#ffcdd2');

                        $events[] = [
                            'id'              => 'holiday-' . $h->id . '-' . $dateStr,
                            'title'           => $h->name,
                            'start'           => $dateStr,
                            'end'             => $dateStr,
                            'allDay'          => true,
                            'backgroundColor' => $bg,
                            'borderColor'     => $bg,
                            'textColor'       => '#000000',
                            'extendedProps'   => [
                                'location'          => null,
                                'supervisors_label' => '',
                                'teammates_label'   => '',
                                'type'              => 'holiday',
                                'company'           => null,
                                'contact'           => null,
                                'tools'             => null,
                                'vehicles'          => null,
                                'notes'             => $h->category === 'pamatny'
                                    ? 'Pamätný deň'
                                    : 'Štátny sviatok / deň pracovného pokoja',
                                'canDelete'         => false,
                            ],
                        ];
                    }
                }

                // meniny
                if ($wantNamedays && isset($namedayIndex[$key]) && $namedayCalendar) {
                    $nd = $namedayIndex[$key];

                    $bg = $namedayCalendar->color ?: '#d1c4e9';

                    $events[] = [
                        'id'              => 'nameday-' . $nd->id . '-' . $dateStr,
                        'title'           => 'Meniny: ' . $nd->names,
                        'start'           => $dateStr,
                        'end'             => $dateStr,
                        'allDay'          => true,
                        'backgroundColor' => $bg,
                        'borderColor'     => $bg,
                        'textColor'       => '#000000',
                        'extendedProps'   => [
                            'location'          => null,
                            'supervisors_label' => '',
                            'teammates_label'   => '',
                            'type'              => 'other',
                            'company'           => null,
                            'contact'           => null,
                            'tools'             => null,
                            'vehicles'          => null,
                            'notes'             => 'Meniny',
                            'canDelete'         => false,
                        ],
                    ];
                }
            }
        }

        return $events;
    }

    /**
     * AJAX create – uloženie udalosti z modálu.
     * + väzba na userAttendance pri type = 'workday'
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $supervisors = $data['supervisors'] ?? [];
        if (is_array($supervisors)) {
            $supervisors = implode(',', array_filter($supervisors));
        }

        $teammates = $data['teammates'] ?? [];
        if (is_array($teammates)) {
            $teammates = implode(',', array_filter($teammates));
        }

        $model = new CalendarEvent();

        $model->calendar_id = isset($data['calendar_id']) ? (int)$data['calendar_id'] : null;
        $model->type        = $data['type']  ?? 'other';
        $model->title       = $data['title'] ?? '';

        $model->start       = $this->normalizeDateTime($data['start'] ?? null);
        $model->end         = $this->normalizeDateTime($data['end'] ?? null);
        $model->all_day     = !empty($data['all_day']) ? 1 : 0;

        $model->location    = $this->nullIfEmpty($data['location'] ?? null);
        $model->supervisors = $this->nullIfEmpty($supervisors);
        $model->teammates   = $this->nullIfEmpty($teammates);
        $model->company     = $this->nullIfEmpty($data['company'] ?? null);
        $model->contact     = $this->nullIfEmpty($data['contact'] ?? null);
        $model->tools       = $this->nullIfEmpty($data['tools'] ?? null);
        $model->vehicles    = $this->nullIfEmpty($data['vehicles'] ?? null);
        $model->notes       = $this->nullIfEmpty($data['notes'] ?? null);

        if (!Yii::$app->user->isGuest) {
            $model->user_id = Yii::$app->user->id;
        }

        $time = time();
        $model->created_at = $time;
        $model->updated_at = $time;

        if ($model->calendar_id === null) {
            return [
                'success' => false,
                'message' => 'Nebolo zvolené ID kalendára.',
            ];
        }

        try {
            if ($model->save()) {
                // IMPORTANT: no userAttendance writes here anymore
                return ['success' => true];
            }

            return [
                'success' => false,
                'errors'  => $model->getErrors(),
                'message' => 'Validation failed.',
            ];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * AJAX delete – zmazanie udalosti + prípadná dochádzka.
     */
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int)Yii::$app->request->post('id');
        if (!$id) {
            return [
                'success' => false,
                'message' => 'Chýba ID udalosti.',
            ];
        }

        $userId = Yii::$app->user->id;

        /** @var CalendarEvent|null $model */
        $model = CalendarEvent::findOne([
            'id'      => $id,
            'user_id' => $userId,
        ]);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'Udalosť neexistuje alebo ju nemôžete zmazať.',
            ];
        }

        try {
            // IMPORTANT: we no longer delete any UserAttendance here
            $model->delete();
            return ['success' => true];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'Chyba pri mazaní udalosti.',
            ];
        }
    }

    /**
     * Auto-fill calendar with work-plan for the logged-in user.
     *
     * Logika:
     *  - akademický rok: september (year) – jún (year+1)
     *  - 1 týždeň škola, 1 týždeň práca (striedavo)
     *  - počas pracovného týždňa každý pracovný deň (Po–Pia) -> workday
     *  - víkendy, štátne sviatky a dni s existujúcou dochádzkou sa preskakujú
     *  - study_plan_items: pre každý mesiac položky zoradené podľa position,
     *    pre každý pracovný deň sa vezme ďalší item (ak dôjdu, začíname odznova)
     */
    public function actionGenerateWorkPlan($year = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'message' => 'Musíte byť prihlásený.',
            ];
        }

        $userId = (int)Yii::$app->user->id;
        /** @var User $user */
        $user   = User::findOne($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Používateľ neexistuje.',
            ];
        }

        if (empty($user->study_plan_type_id)) {
            return [
                'success' => false,
                'message' => 'Používateľ nemá nastavený typ študijného plánu (study_plan_type_id).',
            ];
        }

        // zistíme akademický rok
        $now = new \DateTime();
        if ($year === null) {
            $currentYear  = (int)$now->format('Y');
            $currentMonth = (int)$now->format('n');
            // ak je už september alebo neskôr, akademický rok začína tento rok
            // inak začína minulý rok
            $year = ($currentMonth >= 9) ? $currentYear : $currentYear - 1;
        } else {
            $year = (int)$year;
        }

        // nájdeme (alebo vytvoríme) osobný kalendár
        $personalCalendar = Calendar::findOne([
            'type'    => 'user',
            'user_id' => $userId,
        ]);

        if ($personalCalendar === null) {
            $personalCalendar = new Calendar();
            $personalCalendar->title      = 'Môj kalendár';
            $personalCalendar->type       = 'user';
            $personalCalendar->user_id    = $userId;
            $personalCalendar->color      = '#42a5f5';
            $personalCalendar->created_at = time();
            $personalCalendar->updated_at = time();
            $personalCalendar->save(false);
        }

        // mesiace, ktoré nás zaujímajú (akademický rok)
        $months = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6];

        /** @var StudyPlanItem[] $items */
        $items = StudyPlanItem::find()
            ->where([
                'type_id' => $user->study_plan_type_id,
                'month'   => $months,
            ])
            ->orderBy(['month' => SORT_ASC, 'position' => SORT_ASC])
            ->all();

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'Pre daný študijný plán neexistujú žiadne položky (study_plan_items).',
            ];
        }

        // skupiny položiek per mesiac
        $planByMonth = [];
        foreach ($items as $item) {
            $m = (int)$item->month;
            $planByMonth[$m][] = $item;
        }

        // pointer na ďalší item pre daný mesiac
        $monthItemPointer = []; // [month => index]

        $createdEvents      = 0;
        $createdAttendance  = 0; // kept for response compatibility, but not used now
        $skippedConflicts   = 0;
        $time               = time();
        $classroomLocation  = $user->userclassroom ?? null;

        // dátumový rozsah: 1.9.year – 30.6.(year+1)
        $startDate = new \DateTime(sprintf('%04d-09-01', $year));
        $endDate   = new \DateTime(sprintf('%04d-06-30', $year + 1));

        // nájdeme prvý pondelok od 1.9. => odtiaľ počítame týždne
        $baseMonday = clone $startDate;
        while ($baseMonday->format('N') != 1) { // 1 = pondelok
            $baseMonday->modify('+1 day');
        }

        $baseMondayTs = $baseMonday->getTimestamp();

        // iterujeme po jednotlivých dňoch v akademickom roku
        for ($d = clone $startDate; $d <= $endDate; $d->modify('+1 day')) {
            $monthNum = (int)$d->format('n');

            // ak pre daný mesiac nemáme plán, preskoč
            if (!isset($planByMonth[$monthNum]) || empty($planByMonth[$monthNum])) {
                continue;
            }

            // víkendy nepracujeme
            $dow = (int)$d->format('N'); // 1=Po ... 7=Ne
            if ($dow >= 6) {
                continue;
            }

            // dni pred prvým pondelkom berieme ako "školské" => žiadna práca
            if ($d < $baseMonday) {
                continue;
            }

            // zistíme index týždňa od baseMonday
            $diffDays  = (int)floor(($d->getTimestamp() - $baseMondayTs) / 86400);
            $weekIndex = intdiv($diffDays, 7);

            // párny týždeň = pracovný, nepárny = školský (môžeš otočiť, ak treba)
            $isWorkWeek = ($weekIndex % 2 === 0);
            if (!$isWorkWeek) {
                continue;
            }

            // štátne sviatky => nepracujeme
            if ($this->isPublicHoliday($d)) {
                $skippedConflicts++;
                continue;
            }

            // už existujúca dochádzka => neplánujeme sem
            if ($this->hasAttendance($userId, $d)) {
                $skippedConflicts++;
                continue;
            }

            // vyberieme item pre tento mesiac
            $itemsInMonth = $planByMonth[$monthNum];
            $countItems   = count($itemsInMonth);
            if ($countItems === 0) {
                continue;
            }

            if (!isset($monthItemPointer[$monthNum])) {
                $monthItemPointer[$monthNum] = 0;
            }

            // ak sme na konci zoznamu, začneme odznova (cyklenie)
            $idx  = $monthItemPointer[$monthNum] % $countItems;
            $item = $itemsInMonth[$idx];
            $monthItemPointer[$monthNum]++;

            // dátum a čas udalosti
            $dateStr  = $d->format('Y-m-d');
            $startStr = $dateStr . ' 08:00:00';
            $endStr   = $dateStr . ' 16:00:00';

            // finálna bezpečnostná kontrola dochádzky
            if ($this->hasAttendance($userId, $d)) {
                $skippedConflicts++;
                continue;
            }

            // vytvoríme CalendarEvent typu workday
            $event = new CalendarEvent();
            $event->calendar_id = $personalCalendar->id;
            $event->user_id     = $userId;
            $event->type        = 'workday';
            $event->title       = $item->item;
            $event->start       = $startStr;
            $event->end         = $endStr;
            $event->all_day     = 0;
            $event->location    = $classroomLocation;
            $event->supervisors = null;
            $event->teammates   = null;
            $event->company     = null;
            $event->contact     = null;
            $event->tools       = null;
            $event->vehicles    = null;
            $event->notes       = null;
            $event->created_at  = $time;
            $event->updated_at  = $time;

            if ($event->save(false)) {
                $createdEvents++;
                // NOTE: we no longer create/save UserAttendance here
            } else {
                Yii::error(
                    'Nepodarilo sa vytvoriť CalendarEvent (plan): ' .
                    json_encode($event->getErrors()),
                    __METHOD__
                );
            }
        }

        return [
            'success'            => true,
            'message'            => 'Generovanie pracovného plánu dokončené.',
            'createdEvents'      => $createdEvents,
            'createdAttendance'  => $createdAttendance, // always 0 now
            'skippedConflicts'   => $skippedConflicts,
            'academicYearStart'  => $year,
            'academicYearEnd'    => $year + 1,
        ];
    }

    /**
     * True ak je daný deň štátny / verejný sviatok (nie len pamätný deň).
     */
    protected function isPublicHoliday(\DateTime $date): bool
    {
        $month = (int)$date->format('n');
        $day   = (int)$date->format('j');

        return CalendarHoliday::find()
            ->where(['month' => $month, 'day' => $day])
            ->andWhere(['!=', 'category', 'pamatny']) // pamätný deň = stále sa pracuje
            ->exists();
    }

    /**
     * True ak už má user dochádzku pre daný deň.
     */
    protected function hasAttendance(int $userId, \DateTime $date): bool
    {
        return UserAttendance::find()
            ->where([
                'userId' => $userId,
                'uaDate' => $date->format('Y-m-d'),
            ])
            ->exists();
    }

    protected function normalizeDateTime($value)
    {
        if (empty($value)) {
            return null;
        }
        $value = trim($value);

        $formats = [
            'Y-m-d\TH:i',
            'Y-m-d H:i',
            'Y-m-d H:i:s',
            'm/d/Y h:i A',
            'm/d/Y H:i',
            'd.m.Y H:i',
        ];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt instanceof \DateTime) {
                return $dt->format('Y-m-d H:i:s');
            }
        }

        return $value;
    }

    protected function nullIfEmpty($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }
}
