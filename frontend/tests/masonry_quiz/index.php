<?php
$tools = [
    1 => ['img' => 'eDiqymQZGc.png', 'name' => 'skladací meter'],
    2 => ['img' => 'h1hCqmeAlk.png', 'name' => 'vodováha'],
    3 => ['img' => 'hmqeEiDyS7.png', 'name' => 'murárske kladivo'],
    4 => ['img' => 'KQe34YbbO2.png', 'name' => 'spárovačka'],
    5 => ['img' => 'M6eunloAIg.png', 'name' => 'špachtľa'],
    6 => ['img' => 'Nu0k1xreP8.png', 'name' => 'vysúvaci meter'],
    7 => ['img' => 'pwnjnqAm8a.png', 'name' => 'olovnica'],
    8 => ['img' => 'RwR0kMOIFr.png', 'name' => 'murárska šnúra'],
    9 => ['img' => 'sebPq2cEDi.png', 'name' => 'murárska lyžica'],
    10 => ['img' => 'VmGMajDsQb.png', 'name' => 'hadicová vodováha'],
    11 => ['img' => 'vu0DSKuuer.png', 'name' => 'brúska'],
    12 => ['img' => 'YiqGo8uDM1.png', 'name' => 'gumené kladivo']
];
?>
<!DOCTYPE html>
<html lang="sk">
<!-----------------------    PAGE 1   ----------------------->
<h1>1.1 BOZP a organizácia práce pri murovaní </h1>
<h3>Teoretické východiská:</h3>
1.	Uveďte zásady BOZP pri murovaní <br>
<textarea id="healthPrinciples" name="healthPrinciples" rows="5" cols="33"></textarea><br>
2.	Pomenujte, na aké pásma je rozdelené pracovisko murára a uveďte ich šírku v mm<br>
<textarea id="bricklayerZone" name="bricklayerZone" rows="5" cols="33"></textarea><br>
3.	Uveďte zloženie pracovnej čaty pri murovaní<br>
<textarea id="workingGroups" name="workingGroups" rows="5" cols="33"></textarea><br>
4.	Vymenujte ochranné pracovné prostriedky murára<br>
<textarea id="protectiveEquipment" name="protectiveEquipment" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Popíšte zásady BOZP pri murovaní muriva<br>
<textarea id="safeWorkPrinciples" name="safeWorkPrinciples" rows="5" cols="33"></textarea><br>
2.	Popíšte a vymenujte, aké ochranné pracovné pomôcky potrebujete pri murovaní muriva<br>
<textarea id="describeProtectiveEquipmnet" name="describeProtectiveEquipmnet" rows="5" cols="33"></textarea><br>
3.	Popíšte, na aké pásma by ste rozdelili pracovisko pri murovaní<br>
<textarea id="describeWorkZones" name="describeWorkZones" rows="5" cols="33"></textarea><br>
4.	Z akých pracovníkov sa skladá pracovná čata pri murovaní muriva? <br>
<textarea id="describeBricklayerStaff" name="describeBricklayerStaff" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Ovládam zásady BOZP pri murovaní a organizáciu práce pri murovaní? <br>
<input type="radio" id="basicSafeWorkPrinciples_Yes" name="basicSafeWorkPrinciples" value="Áno">Áno<br>
<input type="radio" id="basicSafeWorkPrinciples_Partially" name="basicSafeWorkPrinciples" value="Čiastočne">Čiastočne<br>
<input type="radio" id="basicSafeWorkPrinciples_No" name="basicSafeWorkPrinciples" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay" name="mistakesDuringSchoolDay" rows="5" cols="33"></textarea><br>
<!-----------------------    PAGE 2   ----------------------->
<h1>1.2 Náradie, nástroje a pomôcky pri murovaní</h1>
<h3>Teoretické východiská:</h3>
1.	Do obrázkov pridajte názov murárskeho náradia:<br>
<?php
session_start();
$score = 0;
$message = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = $_POST['answers'] ?? [];

    foreach ($tools as $id => $tool) {
        $selected = isset($posted[$id]) ? trim($posted[$id]) : '';
        $correct = $tool['name'];
        $isCorrect = ($selected !== '' && $selected === $correct);
        if ($isCorrect) {
            $score++;
        }
        $results[$id] = [
            'selected' => $selected,
            'correct' => $correct,
            'isCorrect' => $isCorrect
        ];
    }

    $message = "$score správnych odpovedí z " . count($tools) . ".";
}


$options = array_column($tools, 'name');
shuffle($options);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kvíz o nástrojoch</title>
    <style>
        .quiz-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /*3 columns x 4 rows */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .question {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
        }

        .tool-image {
            width: 100%;
            height: auto;
            max-width: 220px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            max-width: 260px;
            padding: 6px;
        }

        .message {
            font-size: 1.2em;
            color: #4CAF50;
            margin: 20px 0;
        }

        .feedback {
            margin-top: 8px;
            padding: 6px 8px;
            border-radius: 4px;
            font-weight: 600;
            width: 100%;
            max-width: 260px;
            box-sizing: border-box;
        }
        .feedback.correct {
            background: #e6ffed;
            color: #116927;
            border: 1px solid #9fe6b6;
        }
        .feedback.wrong {
            background: #fff0f0;
            color: #8a1f1f;
            border: 1px solid #f2b3b3;
        }

        @media (max-width: 700px) {
            .quiz-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 701px) and (max-width: 1000px) {
            .quiz-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h1>Priraď obrázky k nástrojom</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="quiz-grid">
            <?php foreach ($tools as $id => $tool): 
                $selectedValue = $results[$id]['selected'] ?? ($_POST['answers'][$id] ?? '');
            ?>
                <div class="question">
                    <img src="images/<?php echo htmlspecialchars($tool['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tool <?php echo $id; ?>" class="tool-image">
                    <select name="answers[<?php echo $id; ?>]">
                        <option value=""> . . . </option>
                        <?php foreach ($options as $option): ?>
                        <option value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php if ($selectedValue !== '' && $selectedValue === $option) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($results)): 
                        $r = $results[$id];
                        if ($r['isCorrect']): ?>
                            <div class="feedback correct">Správne</div>
                        <?php else: ?>
                            <div class="feedback wrong">Správna odpoveď: <?php echo htmlspecialchars($r['correct'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif;
                    endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
            
            <div style="margin-top:20px;">
                <button type="submit">skontroluj odpovede</button>
            </div>
        </form>
    </div>
</body>
2. Uveďte, na čo slúži murárska lyžica<br>
<textarea id="describeTrowelUse" name="describeTrowelUse" rows="5" cols="33"></textarea><br>
3. Uveďte, na čo slúži murárska naberačka<br>
<textarea id="describeMasonryTrowelUse" name="describeMasonryTrowelUse" rows="5" cols="33"></textarea><br>
4. Uveďte, na čo sa používa vodováha<br>
<textarea id="describeLevelUse" name="describeLevelUse" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte BOZP pri práci s pracovným náradím<br>
<textarea id="describeHealthSafetyPrinciples" name="describeHealthSafetyPrinciples" rows="5" cols="33"></textarea><br>
2.	Popíšte a určte druh prác, na ktoré sa používajú pracovné náradia murára<br>
<textarea id="describeToolUsage" name="describeToolUsage" rows="5" cols="33"></textarea><br>

<h3>Sebahodnotenie žiaka:</h3>
1.	Rozoznám pracovné náradia a ovládam BOZP pri práci s pracovným náradím? <br>
<input type="radio" id="recognizeTools_Yes" name="recognizeTools" value="Áno">Áno<br>
<input type="radio" id="recognizeTools_Partially" name="recognizeTools" value="Čiastočne">Čiastočne<br>
<input type="radio" id="recognizeTools_No" name="recognizeTools" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Tools" name="mistakesDuringSchoolDay_Tools" rows="5" cols="33"></textarea><br>

</html>

<!-----------------------    PAGE 3   ----------------------->

<h1>1.3 Základné murárske práce</h1>
<h3>Teoretické východiská:</h3>
1.	Uveďte zásady BOZP pri murovaní<br>
<textarea id="healthPrinciples_2" name="healthPrinciples_2" rows="5" cols="33"></textarea><br>
2.	Pomenujte, na aké pásma je rozdelené pracovisko murára a uveďte ich šírku v mm<br>
<textarea id="bricklayerZone_2" name="bricklayerZone_2" rows="5" cols="33"></textarea><br>
3.	Uveďte zloženie pracovnej čaty pri murovaní<br>
<textarea id="workingGroups_2" name="workingGroups" rows="5" cols="33"></textarea><br>
4.	Vymenujte ochranné pracovné prostriedky murára<br>
<textarea id="protectiveEquipment_2" name="protectiveEquipment_2" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Popíšte a vymenujte, aké ochranné pracovné pomôcky potrebujete pri murovaní muriva<br>
<textarea id="describeProtectiveEquipmnet_2" name="describeProtectiveEquipmnet_2" rows="5" cols="33"></textarea><br>
2.	Popíšte, na aké pásma by ste rozdelili pracovisko pri murovaní.<br>
<textarea id="describeWorkZones_2" name="describeWorkZones_2" rows="5" cols="33"></textarea><br>
3.	Z akých pracovníkov sa skladá pracovná čata pri murovaní muriva?<br>
<textarea id="describeBricklayerStaff_2" name="describeBricklayerStaff_2" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Ovládam správne pracovné postupy pri murovaní? <br>
<input type="radio" id="knowBricklayingProcesses_Yes" name="knowBricklayingProcesses" value="Áno">Áno<br>
<input type="radio" id="knowBricklayingProcesses_Partially" name="knowBricklayingProcesses" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBricklayingProcesses_No" name="knowBricklayingProcesses" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Bricklaying" name="mistakesDuringSchoolDay_Bricklaying" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 4   ----------------------->

<h1>1.4 Väzby tehlového muriva </h1>
<h3>Teoretické východiská:</h3>
1.	Doplňte do obrázka základné rozmery a diely plnej pálenej tehly.<br>
<img src="images/zakladnerozmery_plnej_palenej_tehly.jpg"><br>
H = <input type="text" id="brick_side_H" name="brick_side_H"> mm - šírka tehly<br>
B = <input type="text" id="brick_side_B" name="brick_side_B"> mm - dĺžka tehly<br>
V = <input type="text" id="brick_side_V" name="brick_side_V"> mm - výška tehly<br>
2.	Definujte behúň a väzák<br>
<textarea id="defineStretcherAndBond" name="defineStretcherAndBond" rows="5" cols="33"></textarea><br>
3.	Doplňte názov väzby tehlového muriva<br>
<img src="images/brick1.png"><br>
<textarea id="naBOZPricklayingWeave" name="naBOZPricklayingWeave" rows="5" cols="33"></textarea><br>
4.	Doplňte názov väzby tehlového muriva<br>
<img src="images/brick2.png"><br>
<textarea id="naBOZPrickMasonryBond" name="naBOZPrickMasonryBond" rows="5" cols="33"></textarea><br>
5.	Doplňte názov väzby tehlového muriva<br>
<img src="images/brick3.jpg"><br>   
<textarea id="naBOZPricklayingWeave2" name="naBOZPricklayingWeave2" rows="5" cols="33"></textarea><br>
6.	Doplňte názov väzby tehlového muriva<br>
<img src="images/brick4.jpg"><br>
<textarea id="naBOZPricklayingWeave3" name="naBOZPricklayingWeave3" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte behúňovú väzbu v 1. a 2. vrstve<br>
<textarea id="demonstrateStretcherWeave" name="demonstrateStretcherWeave" rows="5" cols="33"></textarea><br>
2.	Na cvičných tehlách predveďte väzákovú väzbu v 1. a 2.vrstve<br>
<textarea id="demonstrateWeavingOnCubes" name="demonstrateWeavingOnCubes" rows="5" cols="33"></textarea><br>
3.	Na cvičných tehlách predveďte krížovú väzbu v 1. a 2. vrstve<br>
<textarea id="demonstrateNettingOnBricks" name="demonstrateNettingOnBricks" rows="5" cols="33"></textarea><br>
4.	Na cvičných tehlách predveďte polkrížovú väzbu v 1. a 2. vrstve<br>
<textarea id="demonstrateHalfCrossWeaving" name="demonstrateHalfCrossWeaving" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Viem nakresliť a predviesť základné väzby tehlového muriva? <br>
<input type="radio" id="drawAndDemonstrateBasicMasonryBonds_Yes" name="drawAndDemonstrateBasicMasonryBonds" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicMasonryBonds_Partially" name="drawAndDemonstrateBasicMasonryBonds" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicMasonryBonds_No" name="drawAndDemonstrateBasicMasonryBonds" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_MasonryBonds" name="mistakesDuringSchoolDay_MasonryBonds" rows="5" cols="33"></textarea><br>



<!-----------------------    PAGE 5   ----------------------->
<h1>1.5 Väzby ukončenia múru trojštvrťkami  </h1>
<h3>Teoretické východiská:</h3>
1.	Na obrázku je väzba ukončenia muriva hrubého 450 mm trojštvrťkami.<br>
<img src="images/450mmhrubyspojkoncamurivas3.jpg"><br>
Označte správnu odpoveď:<br>
<input type="radio" id="450mmWallEndJoint_Correct" name="450mmWallEndJoint" value="Správne">v 1.vrstve sú trojštvrťky kladené ako behúne a v 2.vrstve ako väzáky<br>
<input type="radio" id="450mmWallEndJoint_Incorrect" name="450mmWallEndJoint" value="Nesprávne">v 1.vrstve sú trojštvrťky kladené ako väzáky a v 2.vrstve ako behúne<br>
<input type="radio" id="450mmWallEndJoint_Incorrect" name="450mmWallEndJoint" value="Nesprávne">v 1. aj v 2.vrstve su kladené ako behúne<br>
2.	Aké sú zásady preväzovania pri ukončovaní múrov trojštvrťkami<br>
<textarea id="basicPrinciplesStringing" name="basicPrinciplesStringing" rows="5" cols="33"></textarea><br>
3.	Nakreslite ukončenie muriva hr. 600 mm trojštvrťkami - 1.vrstvu<br>
4.	Nakreslite ukončenie muriva hr. 600 mm trojštvrťkami - 2.vrstvu<br>

<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte ukončenie muriva hr.450 mm v 1. a 2. vrstve<br>
2.	Na cvičných tehlách predveďte ukončenie muriva hr.600 mm v 1.a 2.vrstve<br>
3.	Popíšte základy preväzovania pri ukončovaní múrov trojštvrťkam<br>
<textarea id="describeBasicsWeavingOnThreeQuartersWalls" name="describeBasicsWeavingOnThreeQuartersWalls" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
3.	Viem nakresliť a predviesť základné väzby ukončovania tehlového muriva trojštvrťkami? <br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithTriples_Yes" name="drawAndDemonstrateBasicsFinishingMasonryWithTriples" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithTriples_Partially" name="drawAndDemonstrateBasicsFinishingMasonryWithTriples" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithTriples_No" name="drawAndDemonstrateBasicsFinishingMasonryWithTriples" value="Nie">Nie<br>
4.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_WallEndWithTriples" name="mistakesDuringSchoolDay_WallEndWithTriples" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 6   ----------------------->
<h1>1.6 Väzby ukončenia múru dlhými polovičkami </h1>
<h3>Teoretické východiská:</h3>
1. Na obrázku je väzba ukončenia muriva hrubého 600 mm dlhými polovičkami<br>
<img src="images/murivo1.jpg"><br>
Označte správnu odpoveď:<br>
<input type="radio" id="600mmWallEndWithLongHalves_Correct" name="600mmWallEndWithLongHalves" value="Správne">v 1. vrstve sú dlhé polovičky kladené ako behúne a v 2.vrstve ako väzáky <br>
<input type="radio" id="600mmWallEndWithLongHalves_Incorrect" name="600mmWallEndWithLongHalves" value="Nesprávne">v 1. vrstve sú dlhé polovičky kladené ako väzáky a v 2.vrstve ako behúne<br>
<input type="radio" id="600mmWallEndWithLongHalves_Incorrect" name="600mmWallEndWithLongHalves" value="Nesprávne">v 1. aj v 2. vrstve sú kladené ako behúne<br>
1.	Aké sú zásady preväzovania pri ukončovaní múrov dlhými polovičkam<br>
<textarea id="basicPrinciplesStringing_LongHalves" name="basicPrinciplesStringing_LongHalves" rows="5" cols="33"></textarea><br>
2.	Nakreslite ukončenie muriva hr. 300 mm dlhými polovičkami - 1.vrstvu<br>
3.	Nakreslite ukončenie muriva hr. 300 mm dlhými polovičkami - 2.vrstvu<br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte ukončenie muriva hr.600 mm v 1. a 2. vrstve<br>
2.	Na cvičných tehlách predveďte ukončenie muriva hr.300 mm v 1.a 2.vrstve<br>
3.	Popíšte základy preväzovania pri ukončovaní múrov dlhými polovičkami<br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Viem nakresliť a predviesť základné väzby ukončovania tehlového muriva dlhými polovičkami? <br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves_Yes" name="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves_Partially" name="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves_No" name="drawAndDemonstrateBasicsFinishingMasonryWithLongHalves" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_WallEndWithLongHalves" name="mistakesDuringSchoolDay_WallEndWithLongHalves" rows="5" cols="33"></textarea><br>



<!-----------------------    PAGE 7   ----------------------->

<h1>1.7 Väzby pravouhlých rohov</h1>
<h3>Teoretické východiská:</h3>
1.	Na obrázku je väzba pravouhlého pripojenia trojštvrťkami.<br>
<img src="images/praverohovespojenie1.jpg"><br>
Označte správnu odpoveď:<br>
<input type="radio" id="RightAngleJointWithTriples_Correct" name="RightAngleJointWithTriples" value="Správne">v 1. vrstve sú trojštvrťky kladené ako behúne a v 2.vrstve ako väzáky<br>
<input type="radio" id="RightAngleJointWithTriples_Incorrect" name="RightAngleJointWithTriples" value="Nesprávne">v 1. vrstve sú trojštvrťky kladené ako väzáky a v 2.vrstve ako behúne<br>
<input type="radio" id="RightAngleJointWithTriples_Incorrect" name="RightAngleJointWithTriples" value="Nesprávne">v 1. aj v 2. vrstve sú kladené ako behúne<br>
2.	Vyznačte na obrázku priebežný múr a pripojený múr v 1. aj v 2. vrstve<br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte 1. a 2. vrstve väzby pravouhlého pripojenia rohov trojštvrťkami<br>
2.	Popíšte zásady preväzovania pri pravouhlom pripojení rohov<br>
<textarea id="describePrinciplesStringing_RightAngleCornerJoints" name="describePrinciplesStringing_RightAngleCornerJoints" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	1.	Viem nakresliť a predviesť základné väzby preväzovania pri pravouhlom pripojení rohov <br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints_Yes" name="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints_Partially" name="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints_No" name="drawAndDemonstrateBasicsJointsInRightAngleCornerJoints" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>

<!-----------------------    PAGE 8   ----------------------->

<h1>1.8 Väzby pravouhlého pripojenia múrov</h1>
<h3>Teoretické východiská:</h3>
1.	Na obrázku je väzba pravouhlého pripojenia múrov trojštvrťkami.<br>
<img src="images/pravouhlespojenie1.jpg"><br>
Označte správnu odpoveď:<br>
<input type="radio" id="RectangularWallJointWithTriples_Correct" name="RectangularWallJointWithTriples" value="Správne">v 1. vrstve sú trojštvrťky kladené ako behúne a v 2.vrstve trojštvrťky nie sú.<br>
<input type="radio" id="RectangularWallJointWithTriples_Incorrect" name="RectangularWallJointWithTriples" value="Nesprávne">v 1. vrstve sú trojštvrťky kladené ako väzáky a v 2.vrstve trojštvrťky nie sú.<br>
<input type="radio" id="RectangularWallJointWithTriples_Incorrect" name="RectangularWallJointWithTriples" value="Nesprávne">v 1. aj v 2. vrstve sú kladené ako väzáky.<br>
2.	Aké sú zásady preväzovania pri pravouhlom pripojení múrov trojštvrťkami<br>
<textarea id="basicPrinciplesStringing_RectangularWallJoints" name="basicPrinciplesStringing_RectangularWallJoints" rows="5" cols="33"></textarea><br>
3.	Vyznačte na obrázku priebežný múr a pripojený múr v 1. aj v 2.vrstve<br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte na 1. a 2. vrstve väzby pravouhlého pripojenia trojštvrťkami<br>
2.	Popíšte zásady preväzovania pri pravouhlom pripojení múrov<br>
<textarea id="describePrinciplesFixing_RectangularWallJoints" name="describePrinciplesFixing_RectangularWallJoints" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Viem nakresliť a predviesť základné väzby preväzovania pri pravouhlom pripojení múrov <br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRectangularWallJoints_Yes" name="drawAndDemonstrateBasicsJointsInRectangularWallJoints" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRectangularWallJoints_Partially" name="drawAndDemonstrateBasicsJointsInRectangularWallJoints" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicsJointsInRectangularWallJoints_No" name="drawAndDemonstrateBasicsJointsInRectangularWallJoints" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_RectangularWallJoints" name="mistakesDuringSchoolDay_RectangularWallJoints" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 9   ----------------------->

<h1>1.9 Väzby stĺpov a pilierov</h1>
<h3>Teoretické východiská:</h3>
1.	Na obrázku sú väzby murovania stĺpov a pilierov trojštvrťkami.<br>
<img src="images/columnandpillarjoints1.jpg"><br>
Podľa obrázka doplňte rozmery stĺpov a pilierov (rozmery si odvoďte z rozmerov tehál – celých a trojštvrtiek)<br>
S1 = <input type="text" id="column_pillar_S1" name="column_pillar_S1"> mm<br>
S2 = <input type="text" id="column_pillar_S2" name="column_pillar_S2"> mm<br>
S3 = <input type="text" id="column_pillar_S3" name="column_pillar_S3"> mm<br>
S4 = <input type="text" id="column_pillar_S4" name="column_pillar_S4"> mm<br>
S5 = <input type="text" id="column_pillar_S5" name="column_pillar_S5"> mm<br>
2.	Aké sú zásady preväzovania primurovaní stĺpov a pilierov trojštvrťkami<br>
<textarea id="basicPrinciplesBinding_ColumnPillarJoints" name="basicPrinciplesBinding_ColumnPillarJoints" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte na 1. a 2. vrstve murovanie stĺpov a pilierov trojštvrťkami podľa obrázka.<br>
2.	Popíšte zásady preväzovania pri murovaní stĺpov a pilierov<br>
3.	Odmerajte rozmery stĺpov predvádzaných na cvičných tehlách a porovnajte ich s rozmermi s cvičenia č.1 – teoretické východiská<br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Viem nakresliť a predviesť základné väzby murovania stĺpov a pilierov ? <br>
<input type="radio" id="drawAndDemonstrateBasicsMasonryInColumnsAndPillars_Yes" name="drawAndDemonstrateBasicsMasonryInColumnsAndPillars" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateBasicsMasonryInColumnsAndPillars_Partially" name="drawAndDemonstrateBasicsMasonryInColumnsAndPillars" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateBasicsMasonryInColumnsAndPillars_No" name="drawAndDemonstrateBasicsMasonryInColumnsAndPillars" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ColumnPillarJoints" name="mistakesDuringSchoolDay_ColumnPillarJoints" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 10   ----------------------->

<h1>1.10 Väzby komínových telies</h1>
<h3>Teoretické východiská:</h3>
1.	Na obrázku je väzba murovania jednoprieduchového komína.<br>
<img src="images/komin1.jpg"><br>
a) Podľa obrázka doplňte rozmery jednoprieduchového komína<br>
<textarea id="chimney1_dimensions" name="chimney1_dimensions" rows="5" cols="33"></textarea><br>
b) Nakreslite väzbu murovania dvojprieduchového komína v 1. a 2. vrstve<br>
2.	Na obrázku je väzba murovania trojprieduchového komína.<br>
<img src="images/komin2.jpg"><br>
a) Podľa obrázka doplňte rozmery trojprieduchového komína<br>
<textarea id="chimney2_dimensions" name="chimney2_dimensions" rows="5" cols="33"></textarea><br>
b) Napíšte pravidlá, ktoré sa musia dodržiavať pri murovaní komínov<br>
<textarea id="rulesForMasonryChimneys" name="rulesForMasonryChimneys" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Na cvičných tehlách predveďte na 1. a 2. vrstve murovanie komínových telies podľa obrázka<br>
2.  Popíšte zásady preväzovania pri murovaní komínových telies<br>
<textarea id="describeBasicPrinciplesBinding_ChimneyMasonry" name="describeBasicPrinciplesBinding_ChimneyMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Viem nakresliť a predviesť väzby murovania komínových telies ?<br>
<input type="radio" id="drawAndDemonstrateMasonryElements_ChimneyBodies_Yes" name="drawAndDemonstrateMasonryElements_ChimneyBodies" value="Áno">Áno<br>
<input type="radio" id="drawAndDemonstrateMasonryElements_ChimneyBodies_Partially" name="drawAndDemonstrateMasonryElements_ChimneyBodies" value="Čiastočne">Čiastočne<br>
<input type="radio" id="drawAndDemonstrateMasonryElements_ChimneyBodies_No" name="drawAndDemonstrateMasonryElements_ChimneyBodies" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ChimneyBodies" name="mistakesDuringSchoolDay_ChimneyBodies" rows="5" cols="33"></textarea><br>



<!-----------------------    PAGE 11   ----------------------->

<h1>1.11 Murovanie v zime a BOZP pri murovaní</h1>
<h3>Teoretické východiská:</h3>
1.	Vymenujte zásady priebežného kontrolovania presnosti muriva<br>
<textarea id="basicPrinciplesContinuousMonitoring" name="basicPrinciplesContinuousMonitoring" rows="5" cols="33"></textarea><br>
2.	Vysvetlite podmienky murovania pri nízkych teplotách - opatrenia<br>
<textarea id="explainMasonryConditions_LowTemperatures" name="explainMasonryConditions_LowTemperatures" rows="5" cols="33"></textarea><br>
3.	Opíšte zabezpečenie murárskych prác v zime<br>
<textarea id="describeProvision_WinterMasonryWorks" name="describeProvision_WinterMasonryWorks" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Určite postup kontroly pri murovaní<br>
<textarea id="defineMasonryControlProcedure" name="defineMasonryControlProcedure" rows="5" cols="33"></textarea><br>
2.	Určite postup murovania a použitia materiálov pri murovaní -5˚C<br>
<textarea id="defineMasonryProcess_MaterialsUsage_Negative5C" name="defineMasonryProcess_MaterialsUsage_Negative5C" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Ovládam pracovný postup pri murovaní v zime a praktickú činnosť s tým súvisiacu? <br>
<input type="radio" id="knowWinterMasonryProcessAndRelatedPracticalActivities_Yes" name="knowWinterMasonryProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowWinterMasonryProcessAndRelatedPracticalActivities_Partially" name="knowWinterMasonryProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowWinterMasonryProcessAndRelatedPracticalActivities_No" name="knowWinterMasonryProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_WinterMasonry" name="mistakesDuringSchoolDay_WinterMasonry" rows="5" cols="33"></textarea><br>



<!-----------------------    PAGE 12   ----------------------->

<h1>2.1  BOZP pri murovaní komínov a predpisy o komínoch</h1>
<h3>Teoretické východiská:</h3>
1.	Uveďte zásady BOZP pri zhotovení komínového telesa<br>
<textarea id="defineHealthSafetyPrinciples_ChimneyConstruction" name="defineHealthSafetyPrinciples_ChimneyConstruction" rows="5" cols="33"></textarea><br>
2.	Uveďte osobné ochranné pomôcky pracovníka pri zhotovení komínového telesa<br>
<textarea id="personalProtectiveEquipment_Worker_ChimneyDesign" name="personalProtectiveEquipment_Worker_ChimneyDesign" rows="5" cols="33"></textarea><br>
3.	Popíšte jednotlivé časti komína:<br>
<img src="images/kominparts.jpg"><br>
1 - <input type="text" id="chimneyPart_1" name="chimneyPart_1"><br>
2 - <input type="text" id="chimneyPart_2" name="chimneyPart_2"><br>
3 - <input type="text" id="chimneyPart_3" name="chimneyPart_3"><br>
4 - <input type="text" id="chimneyPart_4" name="chimneyPart_4"><br>
5 - <input type="text" id="chimneyPart_5" name="chimneyPart_5"><br>
6 - <input type="text" id="chimneyPart_6" name="chimneyPart_6"><br>
L1 - <input type="text" id="chimneyPart_L1" name="chimneyPart_L1"><br>
L2 - <input type="text" id="chimneyPart_L2" name="chimneyPart_L2"><br>
L - <input type="text" id="chimneyPart_L" name="chimneyPart_L"><br>
4.	Vymenujte osobné ochranné pracovné prostriedky pracovníka<br>
<textarea id="personalProtectiveEquipment_Employee" name="personalProtectiveEquipment_Employee" rows="5" cols="33"></textarea><br>
5.	Podľa počtu prieduchov rozdeľujeme komíny:<br>
<input type="radio" id="chimneyClassification_Simple" name="chimneyClassification" value="jednoduche">Jednoduché - s jedným komínovým prieduchom; kombinované - s dvoma alebo viacerými komínovými ventilátormi<br>
<input type="radio" id="chimneyClassification_Vent" name="chimneyClassification" value="ventilovanéVetrané">Ventilované a vetrané<br>
<input type="radio" id="chimneyClassification_TripleFlue" name="chimneyClassification" value="triOtvory">Tri vetracie otvory<br>
6.	Čo je úlohou komína:<br>
<input type="radio" id="chimneyRole_RemoveCombustionGases" name="chimneyRole" value="odstranenieSpalin">Jeho úlohou je bezpečne uvoľňovať spaliny zo zariadení do ovzdušia<br>
<input type="radio" id="chimneyRole_Con" name="chimneyRole" value="connectionRole">Jeho úlohou je pripojiť sa k zariadeniam<br>
<input type="radio" id="chimneyRole_Filtration" name="chimneyRole" value="filterRole">Jeho úlohou je odstrániť dym do vzduchu<br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Popíšte zásady BOZP pri zhotovení komínového telesa<br>
<textarea id="describePrinciples_BOZP_ChimneyConstruction" name="describePrinciples_BOZP_ChimneyConstruction" rows="5" cols="33"></textarea><br>
2.	Popíšte a vymenujte, aké osobné ochranné pracovné prostriedky potrebujete pre zhotovenie komínu<br>
<textarea id="describePersonalProtectiveEquipment_ChimneyConstruction" name="describePersonalProtectiveEquipment_ChimneyConstruction" rows="5" cols="33"></textarea><br>
3.	Hlavné zásady pri upínaní rezného kotúča do uhlovej brúsky a s jej manipuláciou<br>
<textarea id="mainPrinciples_FixingHandling_AngleGrinderCuttingDisc" name="mainPrinciples_FixingHandling_AngleGrinderCuttingDisc" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Ovládam pracovný postup BOZP a praktickú činnosť s tým súvisiacu? <br>
<input type="radio" id="knowBOZPProcessAndRelatedPracticalActivities_Yes" name="knowBOZPProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowBOZPProcessAndRelatedPracticalActivities_Partially" name="knowBOZPProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBOZPProcessAndRelatedPracticalActivities_No" name="knowBOZPProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_BOZPChimney" name="mistakesDuringSchoolDay_BOZPChimney" rows="5" cols="33"></textarea><br>



<!-----------------------    PAGE 13   ----------------------->


<h1>2.2 Podmienky pre dobrý komínový kryt</h1>
<h3>Teoretické východiská:</h3>
1.	Uveďte podmienky dobrého ťahu komínov<br>
<textarea id="conditionsForGoodChimneyDraft" name="conditionsForGoodChimneyDraft" rows="5" cols="33"></textarea><br>
2.	Vymenujte činitele ovplyvňujúce dobrý ťah komínov<br>
<textarea id="factorsAffectingGoodChimneyDraft" name="factorsAffectingGoodChimneyDraft" rows="5" cols="33"></textarea><br>
3.	Čo je to účinná a neúčinná výška komínov<br>
<textarea id="effectiveIneffectiveChimneyHeight" name="effectiveIneffectiveChimneyHeight" rows="5" cols="33"></textarea><br>
4. Vymenujte tvary prieduchov a popíšte ich podľa obrázka<br>
<img src="images/chimneyflueopenings.jpg"><br>
<textarea id="chimneyFlueOpening_1" name="chimneyFlueOpening_1" rows="5" cols="33"><br>
Popíšte, aký komínový prieduch je na obrázku<br>
<img src="images/chimneyflueopenings2.jpg"><br>
<textarea id="chimneyFlueOpening_2" name="chimneyFlueOpening_2" rows="5" cols="33"><br>
<h3>Postup nadobúdania zručnosti:</h3>
1.	Popíšte zásady dobrého ťahu komínov<br>
<textarea id="describePrinciples_GoodChimneyCover" name="describePrinciples_GoodChimneyCover" rows="5" cols="33"></textarea><br>
2.	Popíšte podmienky dobrého ťahu komínov<br>
<textarea id="describeConditions_GoodChimneyCover" name="describeConditions_GoodChimneyCover" rows="5" cols="33"></textarea><br>
3.	Napíšte, odkiaľ sa meria účinná a neúčinná výška komínov<br>
<textarea id="stateWhereMeasured_EffectiveIneffectiveChimneyHeight" name="stateWhereMeasured_EffectiveIneffectiveChimneyHeight" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1.	Ovládam pracovný postup podmienok dobrého ťahu komínov a praktickú činnosť s tým súvisiacu? <br>
<input type="radio" id="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities_Yes" name="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities_Partially" name="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities_No" name="knowGoodChimneyCoverConditionsProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2.	Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_GoodChimneyCover" name="mistakesDuringSchoolDay_GoodChimneyCover" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 14   ----------------------->

<?php
$chimneys = [
    1 => ['img' => 'chimney1.jpg', 'name' => 'jednoprieduchový komín'],
    2 => ['img' => 'chimney2.jpg', 'name' => 'dvojprieduchový komín'],
    3 => ['img' => 'chimney3.jpg', 'name' => 'komínový sopúch'],
    4 => ['img' => 'chimney4.jpg', 'name' => 'komínový nadstavec']
];
?>


<h1>2.3 Postup pri murovaní a príprave komína</h1>
<h3>Teoretické východiská:</h3>
1.  Priraďte správne názvoslovie časti komína k obrázkom:<br>
<?php
session_start();
$score = 0;
$message = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = $_POST['answers'] ?? [];

    foreach ($chimneys as $id => $chimney) {
        $selected = isset($posted[$id]) ? trim($posted[$id]) : '';
        $correct = $chimney['name'];
        $isCorrect = ($selected !== '' && $selected === $correct);
        if ($isCorrect) {
            $score++;
        }
        $results[$id] = [
            'selected' => $selected,
            'correct' => $correct,
            'isCorrect' => $isCorrect
        ];
    }

    $message = "$score správnych odpovedí z " . count($chimneys) . ".";
}


$options = array_column($chimneys, 'name');
shuffle($options);
?>
<body>
    <div class="quiz-container">
        <h1>Priraď obrázky k kominom</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="quiz-grid">
            <?php foreach ($chimneys as $id => $chimney): 
                $selectedValue = $results[$id]['selected'] ?? ($_POST['answers'][$id] ?? '');
            ?>
                <div class="question">
                    <img src="images/<?php echo htmlspecialchars($chimney['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Chimney <?php echo $id; ?>" class="chimney-image">
                    <select name="answers[<?php echo $id; ?>]">
                        <option value=""> . . . </option>
                        <?php foreach ($options as $option): ?>
                        <option value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php if ($selectedValue !== '' && $selectedValue === $option) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($results)): 
                        $r = $results[$id];
                        if ($r['isCorrect']): ?>
                            <div class="feedback correct">Správne</div>
                        <?php else: ?>
                            <div class="feedback wrong">Správna odpoveď: <?php echo htmlspecialchars($r['correct'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif;
                    endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
            
            <div style="margin-top:20px;">
                <button type="submit">skontroluj odpovede</button>
            </div>
        </form>
    </div>
</body>
2. Vymenujte druhy komínov a popíšte pracovný postup pre murovanie komínov<br>
<textarea id="listTypesOfChimneys_DescribeWorkProcedure" name="listTypesOfChimneys_DescribeWorkProcedure" rows="5" cols="33"></textarea><br>
3. Vymenujte, aký materiál, náradie, nástroje a pomôcky potrebujete pre murovanie komínov<br>
<textarea id="listMaterialsToolsForChimneyMasonry" name="listMaterialsToolsForChimneyMasonry" rows="5" cols="33"></textarea><br>
4. Podľa druhu použitého paliva komíny rozdeľujeme: (zakrúžkujte správnu odpoveď)<br>
<img src="images/fuel.jpg"><br>
<input type="radio" id="chimneyClassification_Gas" name="chimneyClassification" value="Plynnéamurované">Plynné a murované<br>
<input type="radio" id="chimneyClassification_Liquid" name="chimneyClassification" value="Kvapalnéamontované">Kvapalné a montované<br>
<input type="radio" id="chimneyClassification_SolidLiquidGas" name="chimneyClassification" value="Tuhékvapalnéaplynné">Tuhé, kvapalné a plynné<br>
5. Vymenujte druhy komínov podľa opláštenia<br>
<textarea id="listTypesOfChimneys_ByCladding" name="listTypesOfChimneys_ByCladding" rows="5" cols="33"></textarea><br>
6. Uveďte, ktoré ochranné pomôcky používame pri murovaní a montovaní komínov<br>
<textarea id="protectiveEquipment_UsedInChimneyMasonryAndAssembly" name="protectiveEquipment_UsedInChimneyMasonryAndAssembly" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Pomenujte konštrukčné chyby pre murovanie komínov<br>
<textarea id="nameConstructionErrors_ChimneyMasonry" name="nameConstructionErrors_ChimneyMasonry" rows="5" cols="33"></textarea><br>
2. Porovnajte rozdiel medzi jednoplášťovým a dvojplášťovým komínom<br>
<textarea id="compareSingleDoubleShellChimney" name="compareSingleDoubleShellChimney" rows="5" cols="33"></textarea><br>
3. Napíšte hlavné body pracovného postupu pre murovanie komínov<br>
<textarea id="writeMainPoints_WorkProcedure_ChimneyMasonry" name="writeMainPoints_WorkProcedure_ChimneyMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup pre murovanie, montáž komínov a praktickú činnosť s tým súvisiacu? <br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_Yes" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_Partially" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_No" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ChimneyMasonryAssembly" name="mistakesDuringSchoolDay_ChimneyMasonryAssembly" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 15   ----------------------->

<h1>2.4 Založenie komína a úprava komínovej hlavy</h1>
<h3>Teoretické východiská:</h3>
1. Určite správny postup pre založenie komína a úpravu komínovej hlavy<br>
<textarea id="determineCorrectProcedure_FoundationChimney_HeadAdjustment" name="determineCorrectProcedure_FoundationChimney_HeadAdjustment" rows="5" cols="33"></textarea><br>
2. Popíšte komínové hlavy na obrázku<br>
<img src="images/chimneyheads.jpg"><br>
<textarea id="describeChimneyHeads" name="describeChimneyHeads" rows="5" cols="33"></textarea><br>
3. Uveďte dôvod a význam správneho založenia komínov a úpravy komínovej hlavy<br>
<textarea id="stateReason_Significance_FoundationChimney_HeadAdjustment" name="stateReason_Significance_FoundationChimney_HeadAdjustment" rows="5" cols="33"></textarea><br>
4. Aké úpravy komínových hláv poznáte?<br>
<textarea id="whatChimneyHeadAdjustmentsDoYouKnow" name="whatChimneyHeadAdjustmentsDoYouKnow" rows="5" cols="33"></textarea><br>
5. Aké sú zásady pri zakladaní murovaných a systémových komínov?<br>
<textarea id="principles_FoundationMasonry_SystemChimneys" name="principles_FoundationMasonry_SystemChimneys" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte a demonštrujte správny postup pre založenie komína systému Schiedel a úpravu komínovej hlavy<br>
<textarea id="describeAndDemonstrateCorrectProcedure_FoundationChimneyHeadAdjustment_SchiedelSystem" name="describeAndDemonstrateCorrectProcedure_FoundationChimneyHeadAdjustment_SchiedelSystem" rows="5" cols="33"></textarea><br>
2. Založte dvojprieduchový komín s otvormi 150/150<br>
3. Vymenujte hlavné zásady BOZP pri zhotovení a osadení komínovej hlavy<br>
<textarea id="listMainPrinciples_BOZP_ChimneyHeadConstructionAndInstallation" name="listMainPrinciples_BOZP_ChimneyHeadConstructionAndInstallation" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup pre zakladanie komínov, úpravu komínových hláv a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities_Yes" name="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities_Partially" name="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities_No" name="knowChimneyFoundation_ChimneyHeadAdjustmentProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ChimneyFoundation_ChimneyHeadAdjustment" name="mistakesDuringSchoolDay_ChimneyFoundation_ChimneyHeadAdjustment" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 16   ----------------------->

<h1>2.5 Murovanie a montáž komínov</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte spôsoby murovania a montovania komínov<br>
<textarea id="stateMethods_MasonryAndAssembly_Chimneys" name="stateMethods_MasonryAndAssembly_Chimneys" rows="5" cols="33"></textarea><br>
2. Vymenujte, aké typy a druhy murovaných a montovaných komínov poznáte<br>
<textarea id="listTypes_MasonryAndMountedChimneys" name="listTypes_MasonryAndMountedChimneys" rows="5" cols="33"></textarea><br>
3. Aké zásady pri murovaní a montovaní komínov poznáte?<br>
<textarea id="whatPrinciples_DoYouKnow_MasonryAndAssembly_Chimneys" name="whatPrinciples_DoYouKnow_MasonryAndAssembly_Chimneys" rows="5" cols="33"></textarea><br>
4. Uveďte, ktoré OOPP používame pri murovaní a montovaní komínov<br>
<textarea id="statePPE_UsedInMasonryAndAssembly_Chimneys" name="statePPE_UsedInMasonryAndAssembly_Chimneys" rows="5" cols="33"></textarea><br>
5. Popíšte čo je na obrázku<br>
<img src="images/chimneymasonry.jpg"><br>
<textarea id="describeImage_ChimneyMasonry" name="describeImage_ChimneyMasonry" rows="5" cols="33"></textarea><br>
6. Aký komínový systém ja zobrazený na obrázku ?<br>
<img src="images/describechimneysystem.jpg"><br>
<img src="images/describechimneysystem2.jpg"><br>
7. Popíšte na čo slúžia komínové hlavice<br>
<img src="images/chimneyhead.jpg"><br>
<img src="images/chimneyhead2.jpg"><br>
<img src="images/chimneyhead3.jpg"><br>
<img src="images/chimneyhead4.jpg"><br>
<textarea id="describePurpose_ChimneyCaps" name="describePurpose_ChimneyCaps" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Vypíšte najčastejšie chyby pri murovaní a montovaní komínov<br>
<textarea id="listCommonMistakes_MasonryAndAssembly_Chimneys" name="listCommonMistakes_MasonryAndAssembly_Chimneys" rows="5" cols="33"></textarea><br>
2. Zmontujte viacvrstvový komín systému Schiedel na cvičnom systéme.<br>
3. Založte dvojprieduchový komín s otvormi 150/150 a 150/215<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup murovania, montovania komínov a praktickú činnosť s tým súvisiacu? <br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_Yes" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_Partially" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities_No" name="knowChimneyMasonryAssemblyProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ChimneyMasonryAssembly" name="mistakesDuringSchoolDay_ChimneyMasonryAssembly" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 17   ----------------------->

<h1>2.6 Kontrola presnosti murovania komínov</h1>
<h3>Teoretické východiská:</h3>
1. Určite systém kontroly presnosti murovania komínov<br>
<textarea id="determineSystem_ControlAccuracy_ChimneyMasonry" name="determineSystem_ControlAccuracy_ChimneyMasonry" rows="5" cols="33"></textarea><br>
2. Vymenujte základné normy pre dodržanie kontroly presnosti murovania a montovania komínových systémov<br>
<textarea id="listBasicStandards_AccuracyControl_ChimneyMasonryAndAssembly" name="listBasicStandards_AccuracyControl_ChimneyMasonryAndAssembly" rows="5" cols="33"></textarea><br>
3. Popíšte postup pre dodržanie zásad kontroly a presnosti murovania komínov<br>
<textarea id="describeProcedure_AdherencePrinciples_ControlAccuracy_ChimneyMasonry" name="describeProcedure_AdherencePrinciples_ControlAccuracy_ChimneyMasonry" rows="5" cols="33"></textarea><br>
4. Ktoré činitele ovplyvňujú činnosť komínových telies a systémov?<br>
<textarea id="whichFactors_AffectFunction_ChimneyBodiesAndSystems" name="whichFactors_AffectFunction_ChimneyBodiesAndSystems" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Rozhodnite, aké komínové murivo alebo systém, by ste uplatnili pri zabudovaní vykurovacieho telesa na tuhé palivo<br>
<textarea id="decideChimneyMasonryOrSystem_ForIncorporation_HeatingBody_SolidFuel" name="decideChimneyMasonryOrSystem_ForIncorporation_HeatingBody_SolidFuel" rows="5" cols="33"></textarea><br>
2. Demonštrujte a uplatnite nadobudnuté zručnosti, vedomosti pri zhotovení komínového systému Schiedel<br>
3. Napíšte hlavné body pre murovanie a montovanie komínových systémov<br>
<textarea id="writeMainPoints_MasonryAndAssembly_ChimneySystems" name="writeMainPoints_MasonryAndAssembly_ChimneySystems" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup kontroly presnosti murovania komínov a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities_Yes" name="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities_Partially" name="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities_No" name="knowChimneyMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ChimneyMasonryAccuracyControl" name="mistakesDuringSchoolDay_ChimneyMasonryAccuracyControl" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 18   ----------------------->

<h1>3.1 BOZP a organizácia práce pri murovaní tvarovkového muriva</h1>
<h3>Teoretické východiská:</h3>
1. Vymenujte hlavné zásady dodržiavania BOZP pri murovaní z tvarovkového muriva<br>
<textarea id="listMainPrinciples_BOZP_ShapeBlockMasonry" name="listMainPrinciples_BOZP_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
2. Vymenujte druhy tvarovkového muriva<br>
<textarea id="listTypes_ShapeBlockMasonry" name="listTypes_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
3. Popíšte všeobecné vlastnosti tvarovkového muriva<br>
<textarea id="describeGeneralProperties_ShapeBlockMasonry" name="describeGeneralProperties_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
4. Uveďte príklad použitia tvarovkového muriva v praxi<br>
<textarea id="giveExample_Application_ShapeBlockMasonry" name="giveExample_Application_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Aké má vlastnosti murivo z tvaroviek POROTHERM a koľko kusov - pozri podľa prospektu potrebujeme do 1m3 tehál 44<br>
<textarea id="whatProperties_ShapeBlockMasonry_POROTHERM" name="whatProperties_ShapeBlockMasonry_POROTHERM" rows="5" cols="33"></textarea><br>
2. Aké sú výhody a nevýhody tvarovkového muriva YTONG a uveďte základný rozmer tvarovky a počet kusov do 1 m2<br>
<textarea id="advantagesDisadvantages_ShapeBlockMasonry_YTONG" name="advantagesDisadvantages_ShapeBlockMasonry_YTONG" rows="5" cols="33"></textarea><br>
3. Definujte organizáciu práce pri murovaní z tvarovkového muriva<br>
<textarea id="defineWorkOrganization_ShapeBlockMasonry" name="defineWorkOrganization_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam zásady BOZP, organizáciu práce pri murovaní tvarožkového muriva a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities_Yes" name="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities_Partially" name="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities_No" name="knowBOZPWorkOrganization_ShapeBlockMasonryProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_BOZPWorkOrganization_ShapeBlockMasonry" name="mistakesDuringSchoolDay_BOZPWorkOrganization_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>


<!-----------------------    PAGE 19   ----------------------->

<h1>3.2 Technológia murovania príprava výber náradia, nástrojov, pomôcok, materiálu na murovanie – výroba a miešanie</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte, aké technológie murovania poznáte<br>
<textarea id="stateTechnologies_Masonry" name="stateTechnologies_Masonry" rows="5" cols="33"></textarea><br>
2. Pomenujte a popíšte pracovné náradia na obrázku, ktoré sa používajú pri murovaní z tvaroviek <br>
<img src="images/masonrytools.jpg"><br>
<img src="images/masonrytools2.jpg"><br>
<textarea id="nameAndDescribe_WorkTools_UsedInShapeBlockMasonry" name="nameAndDescribe_WorkTools_UsedInShapeBlockMasonry" rows="5" cols="33"></textarea><br>
3. Vymenujte vlastnosti tvaroviek – ich výhody a nevýhody<br>
<textarea id="listProperties_ShapeBlocks_AdvantagesDisadvantages" name="listProperties_ShapeBlocks_AdvantagesDisadvantages" rows="5" cols="33"></textarea><br>
4. Vymenujte vhodné náradie, nástroje a pomôcky pre murovanie z tvaroviek<br>
<textarea id="listSuitableTools_InstrumentsForShapeBlockMasonry" name="listSuitableTools_InstrumentsForShapeBlockMasonry" rows="5" cols="33"></textarea><br>
5. Popíšte, na čo sa používajú kotvy z plochej ocele na obrázku<br>
<img src="images/steelanchors.jpg"><br>
<textarea id="describeUse_SteelAnchors" name="describeUse_SteelAnchors" rows="5" cols="33"></textarea><br>
6. Doplňte do textu: Dve urovnávacie lišty nivelačnej súpravy nastavíme pomocou  <input type="text" id="levelingRails_SettingMethod" name="levelingRails_SettingMethod">  skrutiek<br>
<img src="images/levelingrail.jpg"><br>
7. Akým smerom ťaháme nanášací valec: (zakrúžkujte správnu odpoveď)<br>
<img src="images/approller.jpg"><br>
<input type="radio" id="applicationRoller_Direction_Toward_Self" name="applicationRoller_Direction" value="smeromksebe">Výlučne smerom k sebe, t.j. za rukoväťou<br>
<input type="radio" id="applicationRoller_Direction_AwayFrom_Self" name="applicationRoller_Direction" value="smeromodseba">Od seba<br>
<input type="radio" id="applicationRoller_Direction_Behind_Self" name="applicationRoller_Direction" value="zasebou">Za sebou<br>
8. Ako vypočítame obsah Pm - múru: (zakrúžkujte správnu odpoveď)<br>
dl – dĺžka múru<br>
v – výška múru<br>
<input type="radio" id="calculateWallArea_Pm_LengthPlusHeight" name="calculateWallArea_Pm" value="dlplusv">Pm = dl + v<br>
<input type="radio" id="calculateWallArea_Pm_LengthTimesHeight" name="calculateWallArea_Pm" value="dlkratv">Pm = dl x v<br>
<input type="radio" id="calculateWallArea_Pm_LengthMinusHeight" name="calculateWallArea_Pm" value="dlminusv">Pm = dl - v<br>
9. Ako vypočítame obsah Po - okenných otvorov: (zakrúžkujte správnu odpoveď)<br>
š – šírka otvoru<br>
v – výška otvoru<br>
<input type="radio" id="calculateOpeningArea_Po_WidthTimesHeight" name="calculateOpeningArea_Po" value="škratv">Po = š x v<br>
<input type="radio" id="calculateOpeningArea_Po_WidthPlusHeight" name="calculateOpeningArea_Po" value="šplusv">Po = š + v<br>
<input type="radio" id="calculateOpeningArea_Po_HeightMinusWidth" name="calculateOpeningArea_Po" value="šminusv">Po = v - š<br>
10. Ako vypočítame obsah múru P - s otvormi: (zakrúžkujte správnu odpoveď)<br>
Pm – obsah múru<br>
Po – obsah otvorov<br>
<input type="radio" id="calculateWallAreaWithOpenings_P_PmPlusPo" name="calculateWallAreaWithOpenings_P" value="PmplusPo">P = Pm + Po<br>
<input type="radio" id="calculateWallAreaWithOpenings_P_PmMinusPo" name="calculateWallAreaWithOpenings_P" value="PmminusPo">P = Pm - Po<br>
<input type="radio" id="calculateWallAreaWithOpenings_P_PoMinusPm" name="calculateWallAreaWithOpenings_P" value="Pominuspm">P = Po - Pm<br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte všeobecné zásady murovania z tvaroviek<br>
<textarea id="describeGeneralPrinciples_ShapeBlockMasonry" name="describeGeneralPrinciples_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
2. Aké náradie, nástroje a pomôcky používame pre murovanie z tvaroviek<br>
<textarea id="whatTools_Instruments_DoWeUse_ShapeBlockMasonry" name="whatTools_Instruments_DoWeUse_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
3. Demonštrujte založenie muriva na cvičných tvarovkách<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup technológie murovania z tvaroviek a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities_Yes" name="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities_Partially" name="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities_No" name="knowShapeBlockMasonryTechnologyProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ShapeBlockMasonryTechnology" name="mistakesDuringSchoolDay_ShapeBlockMasonryTechnology" rows="5" cols="33"></textarea><br>

<!---------------------    PAGE 20   ----------------------->

<h1>3.3 Prípustné tolerancie pri murovaní tvarovkového muriva</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte prípustné tolerancie pri murovaní z tvaroviek<br>
<textarea id="statePermissibleTolerances_ShapeBlockMasonry" name="statePermissibleTolerances_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
2. Aké tvarovky nie sú vhodné pre použitie v praxi?<br>
<textarea id="whichShapeBlocks_AreNotSuitable_ForUseInPractice" name="whichShapeBlocks_AreNotSuitable_ForUseInPractice" rows="5" cols="33"></textarea><br>
3. Vymenujte dôvody neprípustnosti odchýlky tvarovkového muriva<br>
<textarea id="listReasons_Unacceptability_Deviation_ShapeBlockMasonry" name="listReasons_Unacceptability_Deviation_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
4. Uveďte časté chyby pri murovaní z tvaroviek<br>
<textarea id="stateCommonMistakes_ShapeBlockMasonry" name="stateCommonMistakes_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte chyby, ktoré vidíte na fragmente vymurovania z tvaroviek<br>
<textarea id="describeMistakes_Fragment_ShapeBlockMasonry" name="describeMistakes_Fragment_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
2. Prečo je dôležité aby murivo spĺňalo predpísané normy<br>
<textarea id="whyIsItImportant_WallMasonry_MeetPrescribedStandards" name="whyIsItImportant_WallMasonry_MeetPrescribedStandards" rows="5" cols="33"></textarea><br>
3. Aké tvarovky sú neprípustné pre murovanie<br>
<textarea id="whichShapeBlocks_AreUnacceptable_ForMasonry" name="whichShapeBlocks_AreUnacceptable_ForMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup prípustnej tolerancie a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowPermissibleToleranceProcessAndRelatedPracticalActivities_Yes" name="knowPermissibleToleranceProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowPermissibleToleranceProcessAndRelatedPracticalActivities_Partially" name="knowPermissibleToleranceProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowPermissibleToleranceProcessAndRelatedPracticalActivities_No" name="knowPermissibleToleranceProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_PermissibleTolerance" name="mistakesDuringSchoolDay_PermissibleTolerance" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 21   ----------------------->

<h1>3.4 Murivo z tvaroviek, betónu (napr. Durisol) a pórobetónu</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte vlastnosti betónu a pórobetónu<br>
<textarea id="stateProperties_ConcreteAndAeratedConcrete" name="stateProperties_ConcreteAndAeratedConcrete" rows="5" cols="33"></textarea><br>
2. Opíšte tvarovky Durisol podľa obrázka<br>
<img src="images/durisolblocks.jpg"><br>
<img src="images/durisolblocks2.png"><br>
<textarea id="describeDurisolBlocks" name="describeDurisolBlocks" rows="5" cols="33"></textarea><br>
3. Aká je spotreba materiálu a rozmery tvaroviek (podľa prospektu) Durisol<br>
<textarea id="whatIsMaterialConsumption_Dimensions_DurisolBlocks" name="whatIsMaterialConsumption_Dimensions_DurisolBlocks" rows="5" cols="33"></textarea><br>
4. Vymenujte všeobecné zásady tvarovkového muriva Durisol<br>
<textarea id="listGeneralPrinciples_ShapeBlockMasonry_Durisol" name="listGeneralPrinciples_ShapeBlockMasonry_Durisol" rows="5" cols="33"></textarea><br>
5. Aké základné pravidlá a požiadavky musí spĺňať murivo Durisol<br>
<textarea id="whatBasicRules_Requirements_Masonry_DurisolMustMeet" name="whatBasicRules_Requirements_Masonry_DurisolMustMeet" rows="5" cols="33"></textarea><br>
6. Uveďte použitie tvaroviek Durisol <br>
<textarea id="stateApplication_DurisolBlocks" name="stateApplication_DurisolBlocks" rows="5" cols="33"></textarea><br>
<img src="images/durisolblocks3.jpg"><br>
<img src="images/durisolblocks4.jpg"><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte technologický postup a výhody tvarovkového muriva Durisol a muriva z pórobetónu - uveďte rozdiely<br>
<textarea id="describeTechnologicalProcedure_Advantages_ShapeBlockMasonry_Durisol_And_AeratedConcrete" name="describeTechnologicalProcedure_Advantages_ShapeBlockMasonry_Durisol_And_AeratedConcrete" rows="5" cols="33"></textarea><br>
2. Demonštrujte pracovný postup tvarovkového muriva Durisol na cvičných tvarovkách<br>
3. Uplatnite nadobudnuté poznatky v praktických činnostiach pri murovaní z pórobetónových tvaroviek<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup muriva z tvaroviek, betónu (Drisol), pórobetónu a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities_Yes" name="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities_Partially" name="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities_No" name="knowShapeBlockConcreteAeratedConcreteMasonryProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ShapeBlockConcreteAeratedConcreteMasonry" name="mistakesDuringSchoolDay_ShapeBlockConcreteAeratedConcreteMasonry" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 22   ----------------------->

<h1>3.5 Murivo z keramických tvaroviek</h1>
<h3>Teoretické východiská:</h3>
1. Charakterizujte a popíšte tvarovky Porotherm Profi podľa obrázka<br>
<img src="images/porothermprofi.jpg"><br>
<img src="images/porothermprofi2.jpg"><br>
<textarea id="characterizeAndDescribe_ShapeBlocks_PorothermProfi" name="characterizeAndDescribe_ShapeBlocks_PorothermProfi" rows="5" cols="33"></textarea><br>
2. Uveďte spôsoby murovania systému Profi<br>
<textarea id="stateMasonryMethods_ProfiSystem" name="stateMasonryMethods_ProfiSystem" rows="5" cols="33"></textarea><br>
3. Vymenujte výhody tvarovkového muriva Porotherm<br>
<textarea id="listAdvantages_ShapeBlockMasonry_Porotherm" name="listAdvantages_ShapeBlockMasonry_Porotherm" rows="5" cols="33"></textarea><br>
4. Vysvetlite, ktorá technológia a prečo je vhodná aj pre teploty do – 10 ˚C<br>
<textarea id="explainWhichTechnology_AndWhy_Suitable_TemperaturesToMinus10C" name="explainWhichTechnology_AndWhy_Suitable_TemperaturesToMinus10C" rows="5" cols="33"></textarea><br>
5. Aké druhy výrobkov Porotherm Profi poznáte<br>
<textarea id="whatTypes_Products_PorothermProfiDoYouKnow" name="whatTypes_Products_PorothermProfiDoYouKnow" rows="5" cols="33"></textarea><br>
6. Uveďte aký je rozdiel medzi systémom P + D a systémom Profi – podľa obrázka<br>
<img src="images/porothermpdprofi.png"><br>
<textarea id="stateDifference_System_PlusD_And_Profi" name="stateDifference_System_PlusD_And_Profi" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup systému Profi<br>
<textarea id="describeWorkProcedure_ProfiSystem" name="describeWorkProcedure_ProfiSystem" rows="5" cols="33"></textarea><br>
2. Demonštrujte pracovný postup systému Profi na celoplošnú tenkovrstvovú murovaciu maltu<br>
3. Vysvetlite rozdiel medzi klasickým murovaním a tvarovkovým murovaním<br>
<textarea id="explainDifference_ClassicMasonry_And_ShapeBlockMasonry" name="explainDifference_ClassicMasonry_And_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup murovania muriva z keramických tvaroviek a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities_Yes" name="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities_Partially" name="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities_No" name="knowMasonryProcess_ShapeBlockMasonry_KeramicBlocksAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_ShapeBlockMasonry_KeramicBlocks" name="mistakesDuringSchoolDay_ShapeBlockMasonry_KeramicBlocks" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 23   ----------------------->

<h1>3.6 Kontrola presnosti tvarovkového muriva</h1>
<h3>Teoretické východiská:</h3>
1. Určite systém kontroly presnosti murovania tvarovkového muriva<br>
<textarea id="determineSystem_ControlAccuracy_ShapeBlockMasonry" name="determineSystem_ControlAccuracy_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
2. Vymenujte zásady pre dodržanie kontroly presnosti murovania systému Porotherm<br>
<textarea id="listPrinciples_AccuracyControl_Masonry_PorothermSystem" name="listPrinciples_AccuracyControl_Masonry_PorothermSystem" rows="5" cols="33"></textarea><br>
3. Popíšte postup pre dodržanie zásad kontroly a presnosti tvarovkového muriva<br>
<textarea id="describeProcedure_AdherencePrinciples_ControlAccuracy_ShapeBlockMasonry" name="describeProcedure_AdherencePrinciples_ControlAccuracy_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
4. Ktoré činitele ovplyvňujú horizontálnu a vertikálnu presnosť tvarovkového muriva<br>
<textarea id="whichFactors_Affect_HorizontalAndVerticalAccuracy_ShapeBlockMasonry" name="whichFactors_Affect_HorizontalAndVerticalAccuracy_ShapeBlockMasonry" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Určite postup kontroly pri murovaní z tvaroviek<br>
<textarea id="determineControlProcedure_Masonry_ShapeBlocks" name="determineControlProcedure_Masonry_ShapeBlocks" rows="5" cols="33"></textarea><br>
2. Demonštrujte a uplatnite nadobudnuté vedomosti a zručnosti pri kontrole murovania z tvaroviek<br>
3. Určite prípustnú odchýlku pri murovaní z tvaroviek<br>
<textarea id="determinePermissibleDeviation_Masonry_ShapeBlocks" name="determinePermissibleDeviation_Masonry_ShapeBlocks" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup kontroly presnosti murovania a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities_Yes" name="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities_Partially" name="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities_No" name="knowMasonryAccuracyControlProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_MasonryAccuracyControl" name="mistakesDuringSchoolDay_MasonryAccuracyControl" rows="5" cols="33"></textarea><br>

<!-------------------    PAGE 24   ----------------------->

<h1>4.1 BOZP pri murovaní kamenného a zmiešaného muriva, náradie, nástroje a drobná mechanizácia</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte zásady BOZP pri realizácií kamenného a zmiešaného muriva<br>
<textarea id="stateBOZPPrinciples_StoneAndMixedMasonry" name="stateBOZPPrinciples_StoneAndMixedMasonry" rows="5" cols="33"></textarea><br>
2. Pomenujte jednotlivé pracovné pásma<br>
<textarea id="nameIndividual_WorkZones" name="nameIndividual_WorkZones" rows="5" cols="33"></textarea><br>
3. Vymenujte potrebné náradie, nástroje a drobnú mechanizáciu pri murovaní kamenného a zmiešaného muriva<br>
<textarea id="listNecessary_Tools_Instruments_SmallMechanization_StoneAndMixedMasonry" name="listNecessary_Tools_Instruments_SmallMechanization_StoneAndMixedMasonry" rows="5" cols="33"></textarea><br>
4. Ktoré ochranné prostriedky používa murár pri opracovaní kameňa<br>
<textarea id="whichProtectiveMeasures_UsesMason_StoneProcessing" name="whichProtectiveMeasures_UsesMason_StoneProcessing" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte zásady BOZP pri murovaní z kameňa<br>
<textarea id="describeBOZPPrinciples_StoneMasonry" name="describeBOZPPrinciples_StoneMasonry" rows="5" cols="33"></textarea><br>
2. Popíšte a vymenujte všetky osobné ochranné prostriedky murára<br>
<textarea id="describeAndList_AllPersonalProtectiveMeasures_Mason" name="describeAndList_AllPersonalProtectiveMeasures_Mason" rows="5" cols="33"></textarea><br>
3. Prakticky predveďte správnu manipuláciu s drobnou mechanizáciou<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam zásady BOZP a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowBOZP_PracticalActivities_StoneAndMixedMasonry_Yes" name="knowBOZP_PracticalActivities_StoneAndMixedMasonry" value="Áno">Áno<br>
<input type="radio" id="knowBOZP_PracticalActivities_StoneAndMixedMasonry_Partially" name="knowBOZP_PracticalActivities_StoneAndMixedMasonry" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBOZP_PracticalActivities_StoneAndMixedMasonry_No" name="knowBOZP_PracticalActivities_StoneAndMixedMasonry" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_BOZP_StoneAndMixedMasonry" name="mistakesDuringSchoolDay_BOZP_StoneAndMixedMasonry" rows="5" cols="33"></textarea><br>

<!-------------------    PAGE 25   ----------------------->

<h1>4.2 Výber a úprava kameňa, výroba malty na murovanie kamenného a zmiešaného muriva</h1>
<h3>Teoretické východiská:</h3>
1. Aké sú vhodné kamene pre opracovanie<br>
<textarea id="whatAreSuitableStones_ForProcessing" name="whatAreSuitableStones_ForProcessing" rows="5" cols="33"></textarea><br>
2. Popíšte kamene vhodné na kamenné murivo – podľa obrázka<br>
<img src="images/stoneforstonemasonry.jpg"><br>
<img src="images/stoneforstonemasonry2.jpg"><br>
<img src="images/stoneforstonemasonry3.jpg"><br>
<textarea id="describeStones_SuitableForStoneMasonry" name="describeStones_SuitableForStoneMasonry" rows="5" cols="33"></textarea><br>
3. Pomenujte dôvody pre ktoré upravujeme kameň<br>
<textarea id="nameReasons_WeModifyStone" name="nameReasons_WeModifyStone" rows="5" cols="33"></textarea><br>
4. Ručné opracovanie kameňa sa delí na:<br>
<textarea id="manualStoneProcessing_IsDividedInto" name="manualStoneProcessing_IsDividedInto" rows="5" cols="33"></textarea><br>
5. Uveďte, ktoré náradie je vhodné pre ručné opracovanie kameňa podľa obrázka<br>
<img src="images/stonetools.png"><br>
<img src="images/stonetools2.jpg"><br>
<textarea id="stateWhichTools_AreSuitable_ManualStoneProcessing" name="stateWhichTools_AreSuitable_ManualStoneProcessing" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte úpravu kameňa v kamenných lomoch a v kamenárskych dielňach<br>
<textarea id="describeStoneModification_StoneQuarries_StonemasonWorkshops" name="describeStoneModification_StoneQuarries_StonemasonWorkshops" rows="5" cols="33"></textarea><br>
2. Opracujte kameň na hrubo a na čisto<br>
3. Vyrobte maltu pre použitie murovania z kameňa<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup úpravy kameňa, výrobu malty a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities_Yes" name="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities_Partially" name="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities_No" name="knowStoneModificationProcess_MortarProductionAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_StoneModification_MortarProduction" name="mistakesDuringSchoolDay_StoneModification_MortarProduction" rows="5" cols="33"></textarea><br>

<!-------------------    PAGE 26   ----------------------->

<h1>4.3 Založenie kamenného muriva, zabezpečenie rozmerov a väzieb</h1>
<h3>Teoretické východiská:</h3>
1. Aké kamene sú vhodné pre zakladanie muriva z kameňa<br>
<textarea id="whatStones_AreSuitable_ForFoundation_StoneMasonry" name="whatStones_AreSuitable_ForFoundation_StoneMasonry" rows="5" cols="33"></textarea><br>
2. Popíšte všeobecné zásady založenia kamenného muriva<br>
<textarea id="describeGeneralPrinciples_Foundation_StoneMasonry" name="describeGeneralPrinciples_Foundation_StoneMasonry" rows="5" cols="33"></textarea><br>
3. Určite minimálne previazanie a rozmery škár v kamennom murive<br>
<img src="images/stonemasonrybondingandjoints.png"><br>
<img src="images/stonemasonrybondingandjoints2.png"><br>
<textarea id="determineMinimum_StaggeringAndDimensions_Joints_StoneMasonry" name="determineMinimum_StaggeringAndDimensions_Joints_StoneMasonry" rows="5" cols="33"></textarea><br>
4. Ktoré pomôcky používame pri zakladaní muriva z kameňa<br>
<textarea id="whichAids_DoWeUse_Foundation_StoneMasonry" name="whichAids_DoWeUse_Foundation_StoneMasonry" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Vyberte vhodné kamene pre zakladanie muriva (vyberte z cvičných kameňov)<br>
2. Prakticky predveďte založenie kamenného muriva a správne dodržanie väzby na cvičných kameňoch<br>
3. Hlavné zásady BOZP pri zakladaní kamenného muriva<br>
<textarea id="mainBOZPPrinciples_Foundation_StoneMasonry" name="mainBOZPPrinciples_Foundation_StoneMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zakladania kamenného muriva, správne zabezpečenie väzby a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities_Yes" name="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities_Partially" name="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities_No" name="knowFoundationProcess_StoneMasonry_CorrectStaggeringAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Foundation_StoneMasonry" name="mistakesDuringSchoolDay_Foundation_StoneMasonry" rows="5" cols="33"></textarea><br>

<!---------------------    PAGE 27   ----------------------->

<h1>4.4 Kamenné murivo</h1>
<h3>Teoretické východiská:</h3>
1. Vymenujte a popíšte druhy kamenného muriva podľa obrázka<br>
<img src="images/typesofstonemasonry.png"><br>
<img src="images/typesofstonemasonry2.png"><br>
D - <input type="text" id="typeD_Description" name="typeD_Description"> <br>
E - <input type="text" id="typeE_Description" name="typeE_Description"> <br>
F - <input type="text" id="typeF_Description" name="typeF_Description"> <br>
G - <input type="text" id="typeG_Description" name="typeG_Description"> <br>
H - <input type="text" id="typeH_Description" name="typeH_Description"> <br>
J - <input type="text" id="typeJ_Description" name="typeJ_Description"> <br>
2. Popíšte technologický postup neomietaného muriva z lomového kameňa<br>
<textarea id="describeTechnologicalProcedure_UnplasteredMasonry_RoughStone" name="describeTechnologicalProcedure_UnplasteredMasonry_RoughStone" rows="5" cols="33"></textarea><br>
3. Aké murivo podľa úpravy líca poznáte<br>
<textarea id="whatMasonry_AccordingToFaceModification_DoYouKnow" name="whatMasonry_AccordingToFaceModification_DoYouKnow" rows="5" cols="33"></textarea><br>
4. Čo treba urobiť, aby murivo z kameňa nepraskalo<br>
<textarea id="whatNeedsToBeDone_ToPreventCracking_StoneMasonry" name="whatNeedsToBeDone_ToPreventCracking_StoneMasonry" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Určite správny pracovný postup murovania z kameňa<br>
<textarea id="determineCorrectWorkProcedure_Masonry_Stone" name="determineCorrectWorkProcedure_Masonry_Stone" rows="5" cols="33"></textarea><br>
2. Demonštrujte jednolícne murivo z veľmi nepravidelných tvarov<br>
3. Čím kotvíme kvádrové murivo<br>
<textarea id="whatDoWeAnchor_QuarrystoneMasonryWith" name="whatDoWeAnchor_QuarrystoneMasonryWith" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup kamenného muriva a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowStoneMasonryProcessAndRelatedPracticalActivities_Yes" name="knowStoneMasonryProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowStoneMasonryProcessAndRelatedPracticalActivities_Partially" name="knowStoneMasonryProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowStoneMasonryProcessAndRelatedPracticalActivities_No" name="knowStoneMasonryProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_StoneMasonry" name="mistakesDuringSchoolDay_StoneMasonry" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 28   ----------------------->

<h1>4.5 Zmiešané murivo</h1>
<h3>Teoretické východiská:</h3>
1. Aké druhy zmiešaného muriva poznáte<br>
<img src="images/mixedmasonrytypes.png"><br>
<img src="images/mixedmasonrytypes2.jpg"><br>
<textarea id="whatTypes_MixedMasonry_DoYouKnow" name="whatTypes_MixedMasonry_DoYouKnow" rows="5" cols="33"></textarea><br>
2. Uveďte spôsoby previazania muriva kameň, tehla<br>
<textarea id="stateMethods_StaggeringMasonry_Stone_Brick" name="stateMethods_StaggeringMasonry_Stone_Brick" rows="5" cols="33"></textarea><br>
3. Aký je význam zmiešaného muriva v praxi<br>
<textarea id="whatIsTheSignificance_MixedMasonry_InPractice" name="whatIsTheSignificance_MixedMasonry_InPractice" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte spôsoby previazania zmiešaného muriva tehla, kameň<br>
<textarea id="describeMethods_StaggeringMixedMasonry_Brick_Stone" name="describeMethods_StaggeringMixedMasonry_Brick_Stone" rows="5" cols="33"></textarea><br>
2. Prakticky predveďte zmiešané murivo kameň, tehla so vzduchovou medzerou na cvičných tehlách a kameňoch<br>
3. Kde by ste použili murivo kombinácií kameň, betón<br>
<img src="images/mixedmasonrystoneconcrete.jpg"><br>
<textarea id="whereWouldYouUse_MasonryCombination_Stone_Concrete" name="whereWouldYouUse_MasonryCombination_Stone_Concrete" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zmiešaného muriva a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMixedMasonryProcessAndRelatedPracticalActivities_Yes" name="knowMixedMasonryProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMixedMasonryProcessAndRelatedPracticalActivities_Partially" name="knowMixedMasonryProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMixedMasonryProcessAndRelatedPracticalActivities_No" name="knowMixedMasonryProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_MixedMasonry" name="mistakesDuringSchoolDay_MixedMasonry" rows="5" cols="33"></textarea><br>

<!----------------------    PAGE 29   ----------------------->

<h1>4.6 Kontrola kvality kamenného a zmiešaného muriva</h1>
<h3>Teoretické východiská:</h3>
1. Kde začína kontrola kvality zmiešaného muriva<br>
<textarea id="whereDoesQualityControl_MixedMasonry_Begin" name="whereDoesQualityControl_MixedMasonry_Begin" rows="5" cols="33"></textarea><br>
2. Akú požadovanú kvalitatívnu hodnotu musí spĺňať kameň<br>
<textarea id="whatRequired_QualitativeValue_MustMeet_Stone" name="whatRequired_QualitativeValue_MustMeet_Stone" rows="5" cols="33"></textarea><br>
3. Uveďte spôsob kontroly hrubých a čistých kamenárskych výrobkov<br>
<textarea id="stateMethod_Control_RoughAndFinishedStonemasonProducts" name="stateMethod_Control_RoughAndFinishedStonemasonProducts" rows="5" cols="33"></textarea><br>
4. Ako upravujeme dilatačné škáry<br>
<textarea id="howDoWeModify_ExpansionJoints" name="howDoWeModify_ExpansionJoints" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Vyberte kvalitný kameň pre murovanie kamenného a zmiešaného muriva<br>
2. Na čo musíme dbať pri vonkajšej kontrole zmiešaného a kamenného muriva<br>
<textarea id="whatMustWePayAttentionTo_ExternalControl_MixedAndStoneMasonry" name="whatMustWePayAttentionTo_ExternalControl_MixedAndStoneMasonry" rows="5" cols="33"></textarea><br>
3. Napíšte hlavné body kontroly kvality kamenného muriva<br>
<textarea id="writeMainPoints_QualityControl_StoneMasonry" name="writeMainPoints_QualityControl_StoneMasonry" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup kontroly kamenného, zmiešaného muriva a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities_Yes" name="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities_Partially" name="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities_No" name="knowStoneMixedMasonryControlProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_StoneMixedMasonryControl" name="mistakesDuringSchoolDay_StoneMixedMasonryControl" rows="5" cols="33"></textarea><br>

<!----------------------    PAGE 30   ----------------------->

<h1>5.1 BOZP pri murovaní, osádzaní a založenie priečok</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte, ktoré ochranné pomôcky používame pri murovaní priečok<br>
<textarea id="stateWhichProtectiveAids_DoWeUse_Masonry_Partitions" name="stateWhichProtectiveAids_DoWeUse_Masonry_Partitions" rows="5" cols="33"></textarea><br>
2. Aké sú požiadavky na priečky a ich rozdelenie<br>
<textarea id="whatAreTheRequirements_Partitions_AndTheirDivision" name="whatAreTheRequirements_Partitions_AndTheirDivision" rows="5" cols="33"></textarea><br>
3. Vymenujte zásady pri zakladaní priečok<br>
<textarea id="listPrinciples_Foundation_Partitions" name="listPrinciples_Foundation_Partitions" rows="5" cols="33"></textarea><br>
4. Ktoré materiály sú vhodné pre murovanie priečok<br>
<img src="images/partitionwallmaterials.jpg"><br>
<img src="images/partitionwallmaterials2.jpg"><br>
<img src="images/partitionwallmaterials3.jpg"><br>
<textarea id="whichMaterials_AreSuitable_ForMasonry_Partitions" name="whichMaterials_AreSuitable_ForMasonry_Partitions" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup pri zakladaní priečok a vyberte potrebné náradie na murovanie<br>
<textarea id="describeWorkProcedure_Foundation_Partitions_AndSelectNecessaryTools_ForMasonry" name="describeWorkProcedure_Foundation_Partitions_AndSelectNecessaryTools_ForMasonry" rows="5" cols="33"></textarea><br>
2. Určite spôsob založenia priečky a vypočítajte spotrebu materiálu podľa údajov v prospekte<br>
<textarea id="determineMethod_Foundation_Partition_AndCalculateMaterialConsumption_AccordingToProspectusData" name="determineMethod_Foundation_Partition_AndCalculateMaterialConsumption_AccordingToProspectusData" rows="5" cols="33"></textarea><br>
3. Podľa výkresu cvične založte priečku na cvičných tehlách a stavivách<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zakladania priečok, bezpečnostné predpisy praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities_Yes" name="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities_Partially" name="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities_No" name="knowFoundationProcess_Partitions_SafetyRegulationsAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Foundation_Partitions" name="mistakesDuringSchoolDay_Foundation_Partitions" rows="5" cols="33"></textarea><br>

<!----------------------    PAGE 31   ----------------------->

<h1>5.2 Murovanie priečok z plných a dierových tehál</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte spôsoby murovania priečok z plných a dierových tehál<br>
<img src="images/partitionwallmasonrymethods.png"><br>
<img src="images/partitionwallmasonrymethods2.png"><br>
<img src="images/partitionwallmasonrymethods3.png"><br>
<img src="images/partitionwallmasonrymethods4.png"><br>
<textarea id="stateMethods_Masonry_Partitions_FullAndHollowBricks" name="stateMethods_Masonry_Partitions_FullAndHollowBricks" rows="5" cols="33"></textarea><br>
2. Vypíšte spotrebu tehál na 1m2, pri hrúbke 10 a 15cm – podľa prospektu<br>
<textarea id="writeBrickConsumption_PerSquareMeter_AtThickness_10And15cm_AccordingToProspectus" name="writeBrickConsumption_PerSquareMeter_AtThickness_10And15cm_AccordingToProspectus" rows="5" cols="33"></textarea><br>
3. Čo je to behúňová väzba a aké škáry poznáte, ich rozmery v mm – charakterizujte podľa obrázka<br>
<img src="images/partitionwallbondingandjoints.png"><br>
<textarea id="whatIsRunningBond_AndWhatJoints_DoYouKnow_TheirDimensionsInMm_CharacterizeAccordingToPicture" name="whatIsRunningBond_AndWhatJoints_DoYouKnow_TheirDimensionsInMm_CharacterizeAccordingToPicture" rows="5" cols="33"></textarea><br>
4. Aké vlastnosti musí spĺňať priečka z plných a dierových tehál<br>
<textarea id="whatProperties_MustMeet_Partition_FullAndHollowBricks" name="whatProperties_MustMeet_Partition_FullAndHollowBricks" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup zhotovenia priečky z plnej tehly<br>
<textarea id="describeWorkProcedure_Construction_Partition_FullBrick" name="describeWorkProcedure_Construction_Partition_FullBrick" rows="5" cols="33"></textarea><br>
2. Prakticky predveďte murovanie behúňovej väzby na cvičných tehlách<br>
3. Vymurujte priečku hr. 100 mm na cvičných materiáloch<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup murovania priečok z plných a dierových tehál a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities_Yes" name="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities_Partially" name="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities_No" name="knowMasonryProcess_Partitions_FullAndHollowBricksAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Masonry_Partitions_FullAndHollowBricks" name="mistakesDuringSchoolDay_Masonry_Partitions_FullAndHollowBricks" rows="5" cols="33"></textarea><br>

<!------------------------    PAGE 32   ----------------------->

<h1>5.3 Murovanie priečok z priečkoviek a tvaroviek</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte typy tvaroviek a ich rozmery<br>
<img src="images/partitionwallblocksandshapeblocks.jpg"><br>
<img src="images/partitionwallblocksandshapeblocks2.jpg"><br>
<img src="images/partitionwallblocksandshapeblocks3.jpg"><br>
<textarea id="stateTypes_ShapeBlocks_AndTheirDimensions" name="stateTypes_ShapeBlocks_AndTheirDimensions" rows="5" cols="33"></textarea><br>
2. Ktoré priečkovky, by ste použili pri zhotovení priečok bytového jadra<br>
<textarea id="whichPartitionBricks_WouldYouUse_ForConstruction_Partitions_OfApartmentCore" name="whichPartitionBricks_WouldYouUse_ForConstruction_Partitions_OfApartmentCore" rows="5" cols="33"></textarea><br>
3. Aké zásady uplatňujeme pri murovaní priečok z tvaroviek<br>
<textarea id="whatPrinciples_DoWeApply_Masonry_Partitions_ShapeBlocks" name="whatPrinciples_DoWeApply_Masonry_Partitions_ShapeBlocks" rows="5" cols="33"></textarea><br>
Vlastnými slovami popíšte rozdiel zhotovenia priečok z tehál malého formátu a priečkoviek<br>
<textarea id="inYourOwnWords_DescribeDifference_Construction_Partitions_SmallFormatBricks_PartitionBricks" name="inYourOwnWords_DescribeDifference_Construction_Partitions_SmallFormatBricks_PartitionBricks" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup murovania priečok z tehál Porotherm Profi<br>
<textarea id="describeWorkProcedure_Masonry_Partitions_Brick_PorothermProfi" name="describeWorkProcedure_Masonry_Partitions_Brick_PorothermProfi" rows="5" cols="33"></textarea><br>
2. Cvične vymurujte podľa výkresu priečku z Ytongu hrúbky 100 mm<br>
3. Demonštrujte murovanie priečky z tehál Profi<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zhotovenia priečok z tvaroviek a priečkoviek a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities_Yes" name="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities_Partially" name="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities_No" name="knowConstructionProcess_Partitions_ShapeBlocks_PartitionBricksAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Construction_Partitions_ShapeBlocks_PartitionBricks" name="mistakesDuringSchoolDay_Construction_Partitions_ShapeBlocks_PartitionBricks" rows="5" cols="33"></textarea><br>

<!------------------------    PAGE 33   ----------------------->

<h1>5.4 Vymurovanie dvojitých priečok so zvukovou izoláciou </h1>
<h3>Teoretické východiská:</h3>
1. Uveďte, z čoho pozostávajú dvojité priečky<br>
<img src="images/doublepartitionwallstructure.png"><br>
<img src="images/doublepartitionwallstructure2.png"><br>
<textarea id="stateWhat_DoublePartitions_ConsistOf" name="stateWhat_DoublePartitions_ConsistOf" rows="5" cols="33"></textarea><br>
2. Pomenujte dôvody pre ktoré zhotovujeme tepelnoizolačné a zvukovoizolačné priečky<br>
<textarea id="nameReasons_ForWhich_WeConstruct_ThermalAndSoundInsulationPartitions" name="nameReasons_ForWhich_WeConstruct_ThermalAndSoundInsulationPartitions" rows="5" cols="33"></textarea><br>
3. Z čoho sa skladá zvukovoizolačná a tepelnoizolačná priečka<br>
<textarea id="what_DoesSoundInsulation_AndThermalInsulationPartition_ConsistOf" name="what_DoesSoundInsulation_AndThermalInsulationPartition_ConsistOf" rows="5" cols="33"></textarea><br>
4. Navrhnite vhodný tepelnoizolačný materiál<br>
<img src="images/thermalinsulationmaterials.jpg"><br>
<img src="images/thermalinsulationmaterials2.jpg"><br>
<img src="images/thermalinsulationmaterials3.jpg"><br>
<img src="images/thermalinsulationmaterials4.jpg"><br>
<textarea id="designSuitable_ThermalInsulationMaterial" name="designSuitable_ThermalInsulationMaterial" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Prakticky predveďte upevnenie zvukovej alebo tepelnej izolácie<br>
2. Popíšte pracovný postup zhotovenia dvojitej priečky na cvičných materiáloch<br>
<textarea id="describeWorkProcedure_Construction_DoublePartition_OnTrainingMaterials" name="describeWorkProcedure_Construction_DoublePartition_OnTrainingMaterials" rows="5" cols="33"></textarea><br>
3. Založte podľa technologického postupu zvukovo alebo tepelnoizolačnú priečku<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zhotovenia dvojitých priečok a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities_Yes" name="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities_Partially" name="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities_No" name="knowConstructionProcess_DoublePartitionsAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Construction_DoublePartitions" name="mistakesDuringSchoolDay_Construction_DoublePartitions" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 34   ----------------------->

<h1>5.5 Technológia zhotovenia priečok, kotvenie priečok do muriva a stropov</h1>
<h3>Teoretické východiská:</h3>
1. Aké priečky podľa konštrukcie poznáte<br>
<textarea id="whatPartitions_AccordingToConstruction_DoYouKnow" name="whatPartitions_AccordingToConstruction_DoYouKnow" rows="5" cols="33"></textarea><br>
2. Vymenujte štandardné hrúbky priečok a materiály pre ich použitie<br>
<textarea id="listStandardThicknesses_Partitions_AndMaterials_ForTheirUse" name="listStandardThicknesses_Partitions_AndMaterials_ForTheirUse" rows="5" cols="33"></textarea><br>
3. Aký má význam kotvenia priečok a popíšte kotvenie podľa obrázka<br>
<img src="images/partitionwallanchoring.jpg"><br>
<textarea id="whatIsTheSignificance_AnchoringPartitions_AndDescribeAnchoring_AccordingToPicture" name="whatIsTheSignificance_AnchoringPartitions_AndDescribeAnchoring_AccordingToPicture" rows="5" cols="33"></textarea><br>
4. Popíšte tuhé, klzné a pružné kotvenie<br>
<img src="images/rigidslidingspringanchoring.jpg"><br>
<img src="images/rigidslidingspringanchoring2.jpg"><br>
<img src="images/rigidslidingspringanchoring3.jpg"><br>
<textarea id="describeRigidSlidingAndSpringAnchoring" name="describeRigidSlidingAndSpringAnchoring" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Podľa výkresu vymurujte cvične jednoduchú priečku z tehly dierovanej<br>
2. Prakticky predveďte pružné kotvenie priečok <br>
3. Zhotovte tuhé kotvenie priečky k obvodovému múru<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup technológie zhotovenia priečok, kotvenie do steny a stropu a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities_Yes" name="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities_Partially" name="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities_No" name="knowConstructionProcess_TechnologyOfPartitions_AnchoringToWallAndCeilingAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Construction_TechnologyOfPartitions_Anchoring" name="mistakesDuringSchoolDay_Construction_TechnologyOfPartitions_Anchoring" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 35   ----------------------->

<h1>5.6 Zhotovenie monolitickej priečky</h1>
<h3>Teoretické východiská:</h3>
1. Aké sú výhody a nevýhody monolitických priečok<br>
<textarea id="whatAreTheAdvantages_AndDisadvantages_MonolithicPartitions" name="whatAreTheAdvantages_AndDisadvantages_MonolithicPartitions" rows="5" cols="33"></textarea><br>
2. Kde by ste použili takúto priečku<br>
<textarea id="whereWouldYouUse_SuchPartition" name="whereWouldYouUse_SuchPartition" rows="5" cols="33"></textarea><br>
3. Aké spôsoby zhotovenia monolitických priečok poznáte<br>
<textarea id="whatMethods_Construction_MonolithicPartitions_DoYouKnow" name="whatMethods_Construction_MonolithicPartitions_DoYouKnow" rows="5" cols="33"></textarea><br>
4. Uveďte, ktoré materiály sa používajú na zhotovenie monolitických priečok<br>
<textarea id="stateWhichMaterials_AreUsed_ForConstruction_MonolithicPartitions" name="stateWhichMaterials_AreUsed_ForConstruction_MonolithicPartitions" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte ako by ste zhotovili Monierovú priečku<br>
<textarea id="describe_HowWouldYouConstruct_MonierPartition" name="describe_HowWouldYouConstruct_MonierPartition" rows="5" cols="33"></textarea><br>
2. Vyberte vhodný materiál a pracovné pomôcky pre zhotovenie Rabicovej priečky<br>
<textarea id="selectSuitable_MaterialAndWorkAids_ForConstruction_RabitzPartition" name="selectSuitable_MaterialAndWorkAids_ForConstruction_RabitzPartition" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup monolitickej priečky a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMonolithicPartitionProcessAndRelatedPracticalActivities_Yes" name="knowMonolithicPartitionProcessAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMonolithicPartitionProcessAndRelatedPracticalActivities_Partially" name="knowMonolithicPartitionProcessAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMonolithicPartitionProcessAndRelatedPracticalActivities_No" name="knowMonolithicPartitionProcessAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_MonolithicPartition" name="mistakesDuringSchoolDay_MonolithicPartition" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 36   ----------------------->

<h1>5.7 Montovanie priečok z dielcov a panelov</h1>
<h3>Teoretické východiská:</h3>
1. Aké montované priečky poznáte<br>
<textarea id="whatMountedPartitions_DoYouKnow" name="whatMountedPartitions_DoYouKnow" rows="5" cols="33"></textarea><br>
2. Vymenujte spôsoby zabezpečenia dielcov<br>
<textarea id="listMethods_Securing_Diecvs" name="listMethods_Securing_Diecvs" rows="5" cols="33"></textarea><br>
3. Ako kotvíme dielce o strop<br>
<textarea id="howDoWeAnchor_Diecvs_ToCeiling" name="howDoWeAnchor_Diecvs_ToCeiling" rows="5" cols="33"></textarea><br>
4. Uveďte zásady montovania dielcov<br>
<textarea id="statePrinciples_Mounting_Diecvs" name="statePrinciples_Mounting_Diecvs" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup montovania priečky z celostenových priečkových panelov<br>
<textarea id="describeWorkProcedure_Mounting_Partition_FromSolidPartitionPanels" name="describeWorkProcedure_Mounting_Partition_FromSolidPartitionPanels" rows="5" cols="33"></textarea><br>
2. Prakticky predveďte povrchovú úpravu dielcov<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup montovania priečok z dielcov a panelov a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities_Yes" name="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities_Partially" name="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities_No" name="knowMountingProcess_Partitions_FromDiecvsAndPanelsAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Mounting_Partitions_FromDiecvsAndPanels" name="mistakesDuringSchoolDay_Mounting_Partitions_FromDiecvsAndPanels" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 37   ----------------------->

<h1>5.8 Zhotovenie priečok zo sklených tvaroviek</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte, kde by ste zhotovili takúto priečku<br>
<textarea id="stateWhere_WouldYouConstruct_SuchPartition" name="stateWhere_WouldYouConstruct_SuchPartition" rows="5" cols="33"></textarea><br>
2. Uveďte vlastnosti a uplatnenie sklobetónovej priečky<br>
<textarea id="stateProperties_AndApplication_GlassConcretePartition" name="stateProperties_AndApplication_GlassConcretePartition" rows="5" cols="33"></textarea><br>
3. Aké materiály by ste použili pri realizácií<br>
<img src="images/glassblockpartitionmaterials.jpg"><br>
<img src="images/glassblockpartitionmaterials2.jpg"><br>
<textarea id="whatMaterials_WouldYouUse_InRealization" name="whatMaterials_WouldYouUse_InRealization" rows="5" cols="33"></textarea><br>
4. Uveďte hlavné zásady murovania priečok zo sklobetónových tvaroviek<br>
<img src="images/glassblockpartitionprinciples.jpg"><br>
<textarea id="stateMainPrinciples_Masonry_Partitions_FromGlassConcreteBlocks" name="stateMainPrinciples_Masonry_Partitions_FromGlassConcreteBlocks" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Určite správny postup zhotovenia sklobetónovej priečky<br>
<textarea id="determineCorrectProcedure_Construction_GlassConcretePartition" name="determineCorrectProcedure_Construction_GlassConcretePartition" rows="5" cols="33"></textarea><br>
2. Vypíšte vhodný materiál pre zhotovenie sklobetónovej priečky<br>
<textarea id="writeSuitableMaterial_ForConstruction_GlassConcretePartition" name="writeSuitableMaterial_ForConstruction_GlassConcretePartition" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zhotovenia sklobetónovej priečky a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities_Yes" name="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities_Partially" name="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities_No" name="knowConstructionProcess_GlassConcretePartitionAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Construction_GlassConcretePartition" name="mistakesDuringSchoolDay_Construction_GlassConcretePartition" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 38   ----------------------->

<h1>5.9 Zhotovenie priečok zo sadrokartónu</h1>
<h3>Teoretické východiská:</h3>
Aké poznáte ľahké deliace steny zo sadrokartónu<br>
<textarea id="whatLightPartitionWalls_FromDrywall_DoYouKnow" name="whatLightPartitionWalls_FromDrywall_DoYouKnow" rows="5" cols="33"></textarea><br>
2. Do akej maximálnej výšky môžeme zhotovovať priečky zo sadrokartónu<br>
<textarea id="toWhatMaximumHeight_CanWeConstruct_Partitions_FromDrywall" name="toWhatMaximumHeight_CanWeConstruct_Partitions_FromDrywall" rows="5" cols="33"></textarea><br>
3. Vymenujte druhy sadrokartónových dosiek<br>
<img src="images/drywalboards.jpg"><br>
<img src="images/drywalboards2.jpg"><br>
<img src="images/drywalboards3.jpg"><br>
<textarea id="listTypes_DrywallBoards" name="listTypes_DrywallBoards" rows="5" cols="33"></textarea><br>
4. Z akých prvkov sa skladá konštrukcia priečky zo sadrokartónu<br>
<img src="images/drywallpartitionstructure.png"><br>
<img src="images/drywallpartitionstructure2.png"><br>
<textarea id="whatElements_DoesStructure_Partition_FromDrywall_ConsistOf" name="whatElements_DoesStructure_Partition_FromDrywall_ConsistOf" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Určite zásady pre správne zhotovenie sadrokartónovej priečky<br>
<textarea id="determinePrinciples_ForCorrectConstruction_DrywallPartition" name="determinePrinciples_ForCorrectConstruction_DrywallPartition" rows="5" cols="33"></textarea><br>
2. Popíšte pracovný postup opracovania sadrokartónových profilov a dosiek<br>
<textarea id="describeWorkProcedure_Processing_DrywallProfiles_AndBoards" name="describeWorkProcedure_Processing_DrywallProfiles_AndBoards" rows="5" cols="33"></textarea><br>
3. Cvične prakticky zhotovte jednoduchú sadrokartónovú dosku - fragment priečky<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zhotovenia sadrokartónovej priečky a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities_Yes" name="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities_Partially" name="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities_No" name="knowConstructionProcess_DrywallPartitionAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Construction_DrywallPartition" name="mistakesDuringSchoolDay_Construction_DrywallPartition" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 39   ----------------------->

<h1>5.10 Osádzanie zárubní súčasne murovaním priečok a kontrola kvality priečok</h1>
<h3>Teoretické východiská:</h3>
1. Na čo dbáme pri kontrole kvality zhotovenia priečok<br>
<textarea id="whatDoWePayAttentionTo_QualityControl_Construction_Partitions" name="whatDoWePayAttentionTo_QualityControl_Construction_Partitions" rows="5" cols="33"></textarea><br>
2. Vymenujte zásady pri osádzaní zárubne do priečky<br>
<textarea id="listPrinciples_Installing_DoorFrame_IntoPartition" name="listPrinciples_Installing_DoorFrame_IntoPartition" rows="5" cols="33"></textarea><br>
3. Uveďte, z akých častí sa skladá zárubňa<br>
<textarea id="stateFromWhichParts_DoorFrame_ConsistsOf" name="stateFromWhichParts_DoorFrame_ConsistsOf" rows="5" cols="33"></textarea><br>
4. Ako a podľa čoho správne osadíte zárubňu do priečky<br>
<textarea id="howAndAccordingToWhat_Correctly_WouldYouInstall_DoorFrame_IntoPartition" name="howAndAccordingToWhat_Correctly_WouldYouInstall_DoorFrame_IntoPartition" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Vymenujte pracovné pomôcky pri osadzovaní zárubní<br>
<textarea id="listWorkAids_Installing_DoorFrames" name="listWorkAids_Installing_DoorFrames" rows="5" cols="33"></textarea><br>
2. Popíšte správny pracovný postup osadenia zárubne do priečky a jej kontrolu<br>
<textarea id="describeCorrectWorkProcedure_Installing_DoorFrame_IntoPartition_AndItsControl" name="describeCorrectWorkProcedure_Installing_DoorFrame_IntoPartition_AndItsControl" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup osádzania zárubní do priečok a jej kontrolu a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities_Yes" name="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities_Partially" name="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities_No" name="knowInstallationProcess_DoorFrames_IntoPartitions_AndItsControlAndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_DoorFrames_IntoPartitions_AndItsControl" name="mistakesDuringSchoolDay_Installation_DoorFrames_IntoPartitions_AndItsControl" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 40   ----------------------->

<h1>6.1 BOZP pri osádzaní okien, dverí, prekladov</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte zásady BOZP pri osádzaní okien, dverí, prekladov<br>
<textarea id="statePrinciples_Bozp_Installing_Windows_Doors_Lintels" name="statePrinciples_Bozp_Installing_Windows_Doors_Lintels" rows="5" cols="33"></textarea><br>
2. Vymenujte osobné ochranné pomôcky pracovníka pri osádzaní<br>
<textarea id="listPersonalProtectiveAids_Worker_Installing" name="listPersonalProtectiveAids_Worker_Installing" rows="5" cols="33"></textarea><br>
3. Čo musíme dodržať pri ručnom osádzaní prekladov<br>
<textarea id="whatMustWeObserve_DuringManual_Installing_Lintels" name="whatMustWeObserve_DuringManual_Installing_Lintels" rows="5" cols="33"></textarea><br>
4. Aké sú zásady pri osádzaní ťažkých prekladov<br>
<textarea id="whatAreThePrinciples_DuringInstalling_HeavyLintels" name="whatAreThePrinciples_DuringInstalling_HeavyLintels" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Demonštrujte prácu s malou mechanizáciou pri osádzaní<br>
2. Popíšte a vymenujte, aké ochranné pracovné pomôcky potrebujeme pri osádzaní stavebných prvkov<br>
<textarea id="describeAndList_WhatProtectiveWorkAids_DoWeNeed_DuringInstalling_BuildingElements" name="describeAndList_WhatProtectiveWorkAids_DoWeNeed_DuringInstalling_BuildingElements" rows="5" cols="33"></textarea><br>
3. Vysvetlite bezpečnostné predpisy, ktoré treba dodržiavať pri osádzaní okien, dverí a prekladov<br>
<textarea id="explainSafetyRegulations_ThatMustBeObserved_DuringInstalling_Windows_Doors_AndLintels" name="explainSafetyRegulations_ThatMustBeObserved_DuringInstalling_Windows_Doors_AndLintels" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam zásady BOZP pri osádzaní okien, dverí, prekladov a organizáciu práce s tým súvisiacu?<br>
<input type="radio" id="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization_Yes" name="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization" value="Áno">Áno<br>
<input type="radio" id="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization_Partially" name="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization_No" name="knowBozpPrinciples_Installing_Windows_Doors_Lintels_AndRelatedWorkOrganization" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Bozp_Installing_Windows_Doors_Lintels" name="mistakesDuringSchoolDay_Bozp_Installing_Windows_Doors_Lintels" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 41   ----------------------->

<h1>6.2 Osádzanie drevených a oceľových zárubn</h1>
<h3>Teoretické východiská:</h3>
1. Z akých častí sa skladá oceľová a drevená zárubňa<br>
<img src="images/steelandwoodendoorframeparts.jpg"><br>
<img src="images/steelandwoodendoorframeparts2.jpg"><br>
<textarea id="fromWhichParts_DoesSteelAndWoodenDoorFrame_ConsistOf" name="fromWhichParts_DoesSteelAndWoodenDoorFrame_ConsistOf" rows="5" cols="33"></textarea><br>
2. Ako rozdeľujeme zárubne podľa umiestnenia závesov<br>
<img src="images/doorframehingelocationtypes.jpg"><br>
<img src="images/doorframehingelocationtypes2.jpg"><br>
<textarea id="howDoWeDivide_DoorFrames_AccordingToHingeLocation" name="howDoWeDivide_DoorFrames_AccordingToHingeLocation" rows="5" cols="33"></textarea><br>
3. Vymenujte zásady osádzania zárubní<br>
<textarea id="listPrinciples_Installing_DoorFrames" name="listPrinciples_Installing_DoorFrames" rows="5" cols="33"></textarea><br>
4. Čo je to váhorys a ako skontrolujeme pravý uhol<br>
<textarea id="whatIs_ThePlumbLine_AndHowDoWeCheck_RightAngle" name="whatIs_ThePlumbLine_AndHowDoWeCheck_RightAngle" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Demonštrujte kontrolu správnosti pravého uhla<br>
2. Podľa projektovej dokumentácie osaďte drevenú zárubňu<br>
3. Cvične predveďte osadenie oceľovej zárubne<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný osadenie drevenej a oceľovej zárubne a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities_Yes" name="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities_Partially" name="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities_No" name="knowInstallationProcess_WoodenAndSteelDoorFrames_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_WoodenAndSteelDoorFrames" name="mistakesDuringSchoolDay_Installation_WoodenAndSteelDoorFrames" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 42   ----------------------->

<h1>6.3 Osadenie dverí plastových a z europrofilov</h1>
<h3>Teoretické východiská:</h3>
1. Ako má byť správne pripravený otvor pred osadením dverí<br>
<textarea id="howShouldBe_CorrectlyPrepared_Opening_BeforeInstalling_Doors" name="howShouldBe_CorrectlyPrepared_Opening_BeforeInstalling_Doors" rows="5" cols="33"></textarea><br>
2. Čo sú to difúzne pásky a vysvetlite ich význam<br>
<img src="images/diffusiontapes.jpg"><br>
<img src="images/diffusiontapes2.jpg"><br>
<textarea id="whatAre_DiffusionTapes_AndExplainTheirSignificance" name="whatAre_DiffusionTapes_AndExplainTheirSignificance" rows="5" cols="33"></textarea><br>
3. Akým spôsobom fixujeme a kotvíme rámy dverí z plastu a z europrofilov<br>
<textarea id="byWhatMethod_DoWeFix_AndAnchor_Frames_OfDoors_FromPlastic_AndEuroProfiles" name="byWhatMethod_DoWeFix_AndAnchor_Frames_OfDoors_FromPlastic_AndEuroProfiles" rows="5" cols="33"></textarea><br>
4. Uveďte, ktoré ochranné pomôcky používame pri manipulácií a osádzaní<br>
<textarea id="state_WhichProtectiveAids_DoWeUse_DuringManipulation_AndInstalling" name="state_WhichProtectiveAids_DoWeUse_DuringManipulation_AndInstalling" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte časté chyby pri montáži dverí<br>
<textarea id="describeCommonMistakes_DuringInstallation_Doors" name="describeCommonMistakes_DuringInstallation_Doors" rows="5" cols="33"></textarea><br>
2. Pripravte si vhodné náradie, pomôcky pre osadenie dverí z europrofilov<br>
3. Cvične správne osaďte podľa nadobudnutých vedomosti dvere z europrofilu<br>
4. Predveďte zapenenie dverného rámu<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup osádzania dverí z plastu a z europrofilu a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities_Yes" name="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities_Partially" name="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities_No" name="knowInstallationProcess_Doors_FromPlastic_AndEuroProfiles_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_Doors_FromPlastic_AndEuroProfiles" name="mistakesDuringSchoolDay_Installation_Doors_FromPlastic_AndEuroProfiles" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 43   ----------------------->

<h1>6.4 Osádzanie okien (okenných rámov pred omietkami)</h1>
<h3>Teoretické východiská:</h3>
1. Ako má byť správne pripravený otvor a okenný rám pre osádzanie<br>
<textarea id="howShouldBe_CorrectlyPrepared_Opening_AndWindowFrame_ForInstalling" name="howShouldBe_CorrectlyPrepared_Opening_AndWindowFrame_ForInstalling" rows="5" cols="33"></textarea><br>
2. O koľko má byť väčší otvor oproti rámu<br>
<textarea id="byHowMuch_ShouldBe_Larger_Opening_ComparedToFrame" name="byHowMuch_ShouldBe_Larger_Opening_ComparedToFrame" rows="5" cols="33"></textarea><br>
3. Aké zásady musia byť dodržané pre správne osadenie okenného rámu<br>
<textarea id="whatPrinciples_MustBeObserved_ForCorrectInstallation_WindowFrame" name="whatPrinciples_MustBeObserved_ForCorrectInstallation_WindowFrame" rows="5" cols="33"></textarea><br>
4. Z akého materiálu môžu byť vyrobené okenné rámy<br>
<textarea id="fromWhatMaterial_CanBe_Made_WindowFrames" name="fromWhatMaterial_CanBe_Made_WindowFrames" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Podľa výkresovej dokumentácie osaďte cvične okenný rám bez začisťovacích prác<br>
2. Popíšte správny pracovný postup osádzania okenného rámu pred omietaním<br>
<textarea id="describeCorrectWorkProcedure_Installing_WindowFrame_BeforePlastering" name="describeCorrectWorkProcedure_Installing_WindowFrame_BeforePlastering" rows="5" cols="33"></textarea><br>
3. Osaďte cvične okenný rám do horizontálnej a vertikálnej roviny<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup osadenia okenných rámov pred omietaním a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities_Yes" name="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities_Partially" name="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities_No" name="knowInstallationProcess_WindowFrames_BeforePlastering_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_WindowFrames_BeforePlastering" name="mistakesDuringSchoolDay_Installation_WindowFrames_BeforePlastering" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 44   ----------------------->

<h1>6.5 Osádzanie okenných a dverných rámov po omietkach a vyspravenie</h1>
<h3>Teoretické východiská:</h3>
1. Určite správny postup založenia a vycentrovania okenného rámu<br>
<textarea id="determineCorrectProcedure_Basing_AndCentering_WindowFrame" name="determineCorrectProcedure_Basing_AndCentering_WindowFrame" rows="5" cols="33"></textarea><br>
2. Pripravte okenný rám a vhodný materiál, pomôcky, náradie pre osadenie<br>
<textarea id="prepare_WindowFrame_AndSuitableMaterial_Aids_Tools_ForInstalling" name="prepare_WindowFrame_AndSuitableMaterial_Aids_Tools_ForInstalling" rows="5" cols="33"></textarea><br>
3. Aký je rozdiel pri osádzaní drevených ( z eurohranolov) a plastových okien a dverí<br>
<textarea id="whatIs_TheDifference_DuringInstalling_Wooden_FromEuroBeams_AndPlastic_Windows_AndDoors" name="whatIs_TheDifference_DuringInstalling_Wooden_FromEuroBeams_AndPlastic_Windows_AndDoors" rows="5" cols="33"></textarea><br>
4. Vymenujte zásady správneho vyspravenia muriva po osadení okien a dverí<br>
<textarea id="listPrinciples_CorrectPatching_Masonry_AfterInstalling_Windows_AndDoors" name="listPrinciples_CorrectPatching_Masonry_AfterInstalling_Windows_AndDoors" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Pripravte okenný rám na osadenie do otvoru<br>
2. Popíšte presný pracovný postup osadenia okna a zhotovenie začiťovacích prác<br>
<textarea id="describeExactWorkProcedure_Installing_Window_AndPerforming_FinishingWorks" name="describeExactWorkProcedure_Installing_Window_AndPerforming_FinishingWorks" rows="5" cols="33"></textarea><br>
3. Prakticky cvične demonštrujte dodatočné vyspravenie omietok<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup osadenia okenných a dverných rámov, vyspravenie omietok a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities_Yes" name="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities_Partially" name="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities_No" name="knowInstallationProcess_WindowAndDoorFrames_Patching_Plaster_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_WindowAndDoorFrames_Patching_Plaster" name="mistakesDuringSchoolDay_Installation_WindowAndDoorFrames_Patching_Plaster" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 45   ----------------------->

<h1>6.6 Použitie malej mechanizácie pri osádzaní a BOZP</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte, akú malú mechanizáciu by ste použili pri osádzaní okenných a dverných rámov<br>
<textarea id="state_WhatSmallMechanization_WouldYouUse_DuringInstalling_WindowAndDoorFrames" name="state_WhatSmallMechanization_WouldYouUse_DuringInstalling_WindowAndDoorFrames" rows="5" cols="33"></textarea><br>
2. Vymenujte výhody využitia malej mechanizácie pri osádzaní<br>
<textarea id="listAdvantages_Utilization_SmallMechanization_DuringInstalling" name="listAdvantages_Utilization_SmallMechanization_DuringInstalling" rows="5" cols="33"></textarea><br>
3. Ktoré druhy vrtákov sú vhodné pre vŕtanie do betónových konštrukcií<br>
<textarea id="whichTypes_Drills_AreSuitable_ForDrilling_IntoConcreteStructures" name="whichTypes_Drills_AreSuitable_ForDrilling_IntoConcreteStructures" rows="5" cols="33"></textarea><br>
4. Akým spôsobom navŕtavame otvory do rôznych materiálov<br>
<textarea id="byWhatMethod_DoWeDrill_Holes_IntoDifferentMaterials" name="byWhatMethod_DoWeDrill_Holes_IntoDifferentMaterials" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Prakticky predveďte použitie jednotlivej malej mechanizácie<br>
2. Navŕtajte otvory podľa predpísaného rozmeru a rozhodnite použitie veľkosti vrtáku a použitie príklepu<br>
3. Pomocou použitia elektrického skrutkovača upevnite kotvy o rám okna a pomocou pištoľou na polyuretánovú penu vyplňte medzeru medzi múrom a oknom<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam použitie malej mechanizácie, BOZP a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities_Yes" name="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities_Partially" name="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities_No" name="knowUsage_SmallMechanization_Bozp_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Usage_SmallMechanization_Bozp" name="mistakesDuringSchoolDay_Usage_SmallMechanization_Bozp" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 46   ----------------------->

<h1>6.7 Osádzanie prekladov otvorov a dodržanie BOZP</h1>
<h3>Teoretické východiská:</h3>
1. Na čo slúžia preklady a aké druhy poznáte<br>
<img src="images/linteltypes.jpg"><br>
<img src="images/linteltypes2.jpg"><br>
<img src="images/linteltypes3.jpg"><br>
<textarea id="whatAre_Lintels_AndWhatTypes_DoYouKnow" name="whatAre_Lintels_AndWhatTypes_DoYouKnow" rows="5" cols="33"></textarea><br>
2. Ktoré druhy prekladov sú vhodné pre rekonštrukcie stavieb<br>
<textarea id="whichTypes_Lintels_AreSuitable_For_Reconstructions_Buildings" name="whichTypes_Lintels_AreSuitable_For_Reconstructions_Buildings" rows="5" cols="33"></textarea><br>
3. Aký materiál, by ste použili pre zhotovenie monolitického prekladu<br>
<textarea id="whatMaterial_WouldYouUse_ForConstruction_MonolithicLintel" name="whatMaterial_WouldYouUse_ForConstruction_MonolithicLintel" rows="5" cols="33"></textarea><br>
4. Vymenujte funkcie prekladov a bezpečnostné predpisy pri osádzaní<br>
<textarea id="listFunctions_Lintels_AndSafetyRegulations_DuringInstalling" name="listFunctions_Lintels_AndSafetyRegulations_DuringInstalling" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Popíšte pracovný postup osadenia monolitického prekladu<br>
<textarea id="describeWorkProcedure_Installing_MonolithicLintel" name="describeWorkProcedure_Installing_MonolithicLintel" rows="5" cols="33"></textarea><br>
2. Cvične vykonajte osadenie keramického prekladu<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup osádzania, zhotovenia prekladov a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities_Yes" name="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities_Partially" name="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities_No" name="knowInstallationProcess_Construction_Lintels_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Installation_Construction_Lintels" name="mistakesDuringSchoolDay_Installation_Construction_Lintels" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 47   ----------------------->

<h1>6.8 Kontrola kvality osadenia okien, dverí, prekladov a BOZP pri osádzaní</h1>
<h3>Teoretické východiská:</h3> 
1. Vymenujte pomôcky pre kontrolu pri osádzaní dverných a okenných profilov<br>
<textarea id="listAids_Control_DuringInstalling_DoorAndWindowProfiles" name="listAids_Control_DuringInstalling_DoorAndWindowProfiles" rows="5" cols="33"></textarea><br>
2. Skontrolujte pravý uhol profilu osádzajúceho prvku a aké spôsoby poznáte<br>
<textarea id="checkRightAngle_Profile_OfInstallingElement_AndWhatMethods_DoYouKnow" name="checkRightAngle_Profile_OfInstallingElement_AndWhatMethods_DoYouKnow" rows="5" cols="33"></textarea><br>
3. Vlastnými slovami vyjadrite čo dosiahneme pomocou váhorysu<br>
<textarea id="inYourOwnWords_Express_WhatWeAchieve_UsingPlumbLine" name="inYourOwnWords_Express_WhatWeAchieve_UsingPlumbLine" rows="5" cols="33"></textarea><br>
4. Ako skontrolujete správnosť vertikálnej roviny<br>
<textarea id="howDoYouCheck_Correctness_VerticalPlane" name="howDoYouCheck_Correctness_VerticalPlane" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Pomocou metra skontrolujte správnosť pravého uhla profilu<br>
2. Demonštrujte nanesenie váhorysu pomocou hadicovej vodováhy alebo optickým prístrojom<br>
3. Premerajte správnosť osadenia parapetu<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup správnosti kontroly osadenia okenných a dverných prvkov a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities_Yes" name="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities_Partially" name="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities_No" name="knowWorkProcedure_CorrectnessControl_Installing_WindowAndDoorElements_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_CorrectnessControl_Installing_WindowAndDoorElements" name="mistakesDuringSchoolDay_CorrectnessControl_Installing_WindowAndDoorElements" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 48   ----------------------->

<h1>7.1 BOZP pri omietaní, ochranné pomôcky, druhy omietok, náradie, nástroje pre omietanie</h1>
<h3>Teoretické východiská:</h3>
1. Uveďte základné bezpečnostné predpisy pri omietaní o použitie osobných prostriedkov<br>
<textarea id="state_BasicSafetyRegulations_DuringPlastering_AboutUsage_PersonalMeans" name="state_BasicSafetyRegulations_DuringPlastering_AboutUsage_PersonalMeans" rows="5" cols="33"></textarea><br>
2. Vymenujte druhy omietok a popíšte na čo slúžia<br>
<textarea id="listTypes_Plaster_AndDescribe_WhatTheyAreUsedFor" name="listTypes_Plaster_AndDescribe_WhatTheyAreUsedFor" rows="5" cols="33"></textarea><br>
3. Pomenujte základné náradie, nástroje a pomôcky pre omietanie<br>
<textarea id="name_BasicTools_Instruments_AndAids_ForPlastering" name="name_BasicTools_Instruments_AndAids_ForPlastering" rows="5" cols="33"></textarea><br>
4. Uveďte, ktoré ochranné pomôcky používame pri omietaní<br>
<textarea id="state_WhichProtectiveAids_DoWeUse_DuringPlastering" name="state_WhichProtectiveAids_DoWeUse_DuringPlastering" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Posúďte, ktorá malta je vhodná pre vnútorné omietky, cementová, vápenno cementová alebo vápenná<br>
2. Prakticky pripravte vhodné náradie, pomôcky a nástroje pre zhotovenie omietok<br>
3. Vypíšte hlavné zásady BOZP pri omietaní<br>
<textarea id="writeMainPrinciples_Bozp_DuringPlastering" name="writeMainPrinciples_Bozp_DuringPlastering" rows="5" cols="33"></textarea><br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam BOZP pri omietaní, účel a druhy omietok, náradie, nástroje pomôcky a druhy omietok a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities_Yes" name="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities_Partially" name="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities_No" name="knowBozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Bozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids" name="mistakesDuringSchoolDay_Bozp_DuringPlastering_Purpose_AndTypesOfPlaster_Tools_Instruments_Aids" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 49   ----------------------->

<h1>7.2 Príprava malty na omietanie a organizácia práce pri ručnom omietaní</h1>
<h3>Teoretické východiská:</h3>
1. Ako pripravíte stavbu pre omietanie<br>
<textarea id="howDoYouPrepare_Construction_ForPlastering" name="howDoYouPrepare_Construction_ForPlastering" rows="5" cols="33"></textarea><br>
2. Z akých členov sa skladá pracovná čata pre ručné omietanie<br>
<textarea id="fromWhichMembers_Does_WorkingTeam_ForManualPlastering_ConsistOf" name="fromWhichMembers_Does_WorkingTeam_ForManualPlastering_ConsistOf" rows="5" cols="33"></textarea><br>
3. Vymenujte zloženie malty<br>
<textarea id="list_Composition_OfMortar" name="list_Composition_OfMortar" rows="5" cols="33"></textarea><br>
4. Aké maltové zmesi poznáte<br>
<textarea id="what_MortarMixtures_DoYouKnow" name="what_MortarMixtures_DoYouKnow" rows="5" cols="33"></textarea><br>
<h3>Postup nadobúdania zručnosti:</h3>
1. Rozhodnite, kedy je stavba pripravená pre zhotovenie omietok<br>
2. Pripravte výrobné jadro pre miešanie malty<br>
3. Vyrobte maltu tradičným spôsobom<br>
<h3>Sebahodnotenie žiaka:</h3>
1. Ovládam pracovný postup zhotovenia malty pre omietanie, organizáciu práce a praktickú činnosť s tým súvisiacu?<br>
<input type="radio" id="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities_Yes" name="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities" value="Áno">Áno<br>
<input type="radio" id="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities_Partially" name="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities" value="Čiastočne">Čiastočne<br>
<input type="radio" id="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities_No" name="knowWorkProcedure_Production_Mortar_ForPlastering_WorkOrganization_AndRelatedPracticalActivities" value="Nie">Nie<br>
2. Aké chyby v pracovnej činnosti som urobil počas vyučovacieho dňa?<br>
<textarea id="mistakesDuringSchoolDay_Production_Mortar_ForPlastering_WorkOrganization" name="mistakesDuringSchoolDay_Production_Mortar_ForPlastering_WorkOrganization" rows="5" cols="33"></textarea><br>

<!-----------------------    PAGE 50   ----------------------->

<h1>7.3 Príprava podkladu pre omietanie, základné spôsoby ručného omietania stien a stropov</h1>
<h3>Teoretické východiská:</h3>

























<!-----------------------    PAGE 64   ----------------------->
</html>