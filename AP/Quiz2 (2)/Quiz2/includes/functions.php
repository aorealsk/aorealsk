<?php
require_once __DIR__ . '/../config.php';

/* ----------------------------------------------------
 * LOAD QUIZ DATA
 * ---------------------------------------------------- */
function loadQuizData() {
    $data = file_get_contents(DATA_FILE);
    return json_decode($data, true);
}

function getQuestions() {
    $data = loadQuizData();
    $questions = [];

    foreach ($data['sections'] as $section) {
        foreach ($section['questions'] as $q) {
            $questions[] = $q;
        }
    }

    return $questions;
}

/* ----------------------------------------------------
 * RENDER QUESTION
 * (ponecháme bez zmien)
 * ---------------------------------------------------- */
function renderQuestion($question, $lang) {

    $t = $question['text'][$lang];
    $html = "<div class='question-card'>";
    $html .= "<h3>{$t}</h3>";

    /* IMAGE DISPLAY IF PRESENT */
    if (!empty($question['image'])) {
        $safeImg = htmlspecialchars($question['image'], ENT_QUOTES, 'UTF-8');
        $html .= "<img src='{$safeImg}' class='question-image' alt='question-image'>";
    }

    switch ($question['type']) {

        case 'single_choice':
            foreach ($question['options'] as $opt) {
                $txt = $opt['text'][$lang];
                $key = $opt['key'];

                $html .= "
                    <label class='option-label'>
                        <input type='radio' name='{$question['id']}' value='{$key}'>
                        <span>$txt</span>
                    </label>
                ";
            }
            break;

        case 'multiple_choice':
            foreach ($question['options'] as $opt) {
                $txt = $opt['text'][$lang];
                $key = $opt['key'];

                $html .= "
                    <label class='option-label'>
                        <input type='checkbox' name='{$question['id']}[]' value='{$key}'>
                        <span>$txt</span>
                    </label>
                ";
            }
            break;

        case 'text_input':
            $ph = $question['placeholder'][$lang] ?? '';
            $html .= "
                <textarea class='text-input' name='{$question['id']}' placeholder='$ph'></textarea>
            ";
            break;

        case 'sort_list':
            $html .= "<ul class='sortable'>";

            foreach ($question['materials'][$lang] as $item) {
                $safe = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');

                $html .= "
                    <li draggable='true'>
                        <input type='hidden' name='answer_{$question['id']}[]' value='$safe'>
                        $safe
                    </li>
                ";
            }

            $html .= "</ul>";
            break;

        case 'fill_table':
            $headers = $question['table_headers'];
            $rows = $question['materials'][$lang];

            $html .= "<table class='quiz-table'><thead><tr>";

            foreach ($headers as $h) {
                $html .= "<th>$h</th>";
            }

            $html .= "</tr></thead><tbody>";

            foreach ($rows as $index => $row) {
                $safe = htmlspecialchars($row, ENT_QUOTES, 'UTF-8');
                $html .= "<tr>";
                $html .= "<td>$safe</td>";

                for ($i = 1; $i < count($headers); $i++) {
                    $html .= "<td><input type='text'
                                name='answer_{$question['id']}[$index][$i]'
                                class='table-input'></td>";
                }

                $html .= "</tr>";
            }

            $html .= "</tbody></table>";
            break;

        case 'image_label':

            foreach ($question['options'] as $opt) {
                $txt = $opt['text'][$lang];
                $key = $opt['key'];

                $html .= "
                    <label class='option-label'>
                        <input type='radio' name='{$question['id']}' value='{$key}'>
                        <span>$txt</span>
                    </label>
                ";
            }
            break;
    }

    $html .= "</div>";
    return $html;
}

/* ----------------------------------------------------
 * EVALUATION (rozšírené -> vracia aj 'details' pre highlight)
 * ---------------------------------------------------- */
function evaluateAnswers($questions, $submitted) {

    $correctCount = 0;
    $totalEvaluated = 0;
    $details = []; // bude obsahovať detail na každu vyhodnotenú otázku

    $lang = $_SESSION['lang'] ?? 'sk';

    foreach ($questions as $q) {

        // skip non-evaluated
        if (isset($q['evaluated']) && $q['evaluated'] === false) {
            continue;
        }

        $entry = [
            'id' => $q['id'],
            'type' => $q['type'],
            'evaluated' => $q['evaluated'] ?? false,
            'is_correct' => null,
            'user' => null,
            'correct' => null
        ];

        /* SINGLE / MULTIPLE / IMAGE LABEL */
        if (in_array($q['type'], ['single_choice', 'multiple_choice', 'image_label'])) {

            // if no correct key defined -> skip evaluation
            if (empty($q['correct'])) {
                $details[] = $entry;
                continue;
            }

            $totalEvaluated++;

            if (!isset($submitted[$q['id']])) {
                // user didn't answer
                $entry['user'] = null;
                $entry['correct'] = $q['correct'];
                $entry['is_correct'] = false;
                $details[] = $entry;
                continue;
            }

            $user = $submitted[$q['id']];
            if ($q['type'] === 'multiple_choice') {
                if (!is_array($user)) $user = [$user];

                $u = $user;
                $c = $q['correct'];

                sort($u);
                sort($c);

                $entry['user'] = $u;
                $entry['correct'] = $c;
                $entry['is_correct'] = ($u === $c);

                if ($entry['is_correct']) $correctCount++;

            } else {
                // single choice / image_label
                if (is_array($user)) $user = $user[0];
                $entry['user'] = $user;
                $entry['correct'] = $q['correct']; // array of correct keys
                $entry['is_correct'] = in_array($user, $q['correct']);
                if ($entry['is_correct']) $correctCount++;
            }

            $details[] = $entry;
            continue;
        }

        /* SORT LIST */
        if ($q['type'] === 'sort_list') {

            $totalEvaluated++;

            $field = "answer_" . $q['id'];
            $userOrder = $submitted[$field] ?? [];

            $entry['user'] = $userOrder;
            $entry['correct'] = $q['correct_order'] ?? [];

            if (empty($userOrder)) {
                $entry['is_correct'] = false;
                $details[] = $entry;
                continue;
            }

            // normalize compare via translation to EN (consistent with stored correct_order)
            $translated = [];
            $ok = true;

            foreach ($userOrder as $item) {
                // find index in materials for current language
                $found = false;
                foreach ($q['materials'][$lang] as $idx => $m) {
                    if (trim($m) === trim($item)) {
                        $translated[] = $q['materials']['en'][$idx] ?? null;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $ok = false;
                    break;
                }
            }

            if ($ok && isset($q['correct_order'])) {
                // normalized comparison
                $normTranslated = array_map(function($s){ return trim($s); }, $translated);
                $normCorrect = array_map(function($s){ return trim($s); }, $q['correct_order']);
                $entry['is_correct'] = ($normTranslated === $normCorrect);
                if ($entry['is_correct']) $correctCount++;
                // save translated representation (EN) and also keep user (lang)
                $entry['translated_en'] = $translated;
            } else {
                $entry['is_correct'] = false;
            }

            $details[] = $entry;
            continue;
        }

        // other types (not evaluated)
        $details[] = $entry;
    }

    $percent = $totalEvaluated > 0 ? round(($correctCount / $totalEvaluated) * 100, 2) : 0;

    return [
        'correct' => $correctCount,
        'total' => $totalEvaluated,
        'percent' => $percent,
        'details' => $details
    ];
}
