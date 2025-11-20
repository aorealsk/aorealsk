<?php
/**
 * @var $data
 */
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

$this->title = 'Developer Test';
$qrUrl  = Yii::getAlias('@web') . '/frontend/views/dev-test/asset/devtest_qrcode.png';
?>

<main class="site-student">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/header-bg1.jpg');" width="1600" height="400"></canvas>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong></h1>
                </div>
            </div>
        </div>
        <div class="breadcrumbs-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?=
                        Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dev-test" class="container-fluid">
        <div class="dev-container">

            <form method="post" role="form" id="dev-form">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam; ?>" value="<?= \Yii::$app->request->getCsrfToken() ?>">
                <div class="card zero-radius">
                    <div class="card-body">

                    <!-- QR between the banner and the form -->
                            <div class="qr-bridge">
                                <div class="qr-card">
                                    <img src="<?= Html::encode($qrUrl) ?>" alt="Open Days – QR kód">
                                        <small>aoreal.sk/dev-test</small>
                                </div>
                            </div>


                        <h3>Developer Test - PHP + Sql + Docker + Python</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><b>Firstname</b></label>
                                    <input
                                            type="text"
                                            class="form-control re-data"
                                            name="Quiz[name_first]"
                                            value="<?= $data['name_first'] ?? '' ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><b>Lastname</b></label>
                                    <input
                                            type="text"
                                            class="form-control re-data"
                                            name="Quiz[name_last]"
                                            value="<?= $data['name_last'] ?? '' ?>"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"><b>Email</b></label>
                                    <input
                                            type="email"
                                            name="Quiz[email]"
                                            class="form-control re-data"
                                            value="<?= $data['email'] ?? '' ?>"
                                    >
                                </div>
                            </div>
                        </div>

                        <h5 style="margin-top: 30px"><b>Rate your skills!</b></h5>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-md-12">
                                <?php
                                $skills = [
                                    'html5' => 'HTML5',
                                    'css3' => 'CSS3',
                                    'tail' => 'TailwindCss',
                                    'php8' => 'PHP8',
                                    'symfony' => 'Symfony',
                                    'laravel' => 'Laravel',
                                    'pack' => 'Package managers (NuGet, composer)',
                                    'linux' => 'Linux',
                                    'win' => 'Windows',
                                    'js' => 'JS',
                                    'jquery' => 'Jquery',
                                    'docker' => 'Docker',
                                    'k8s' => 'Kubernetes',
                                    'cicd' => 'CI/CD',
                                    'git' => 'Git',
                                    'unit' => 'Unit testing',
                                    'chaos' => 'Chaos testing',
                                    'mysql' => 'Mysql',
                                    'python' => 'Python',
                                    'scraping' => 'Web Scraping',
                                    'ai' => 'AI',
                                    'ml' => 'Machine Learning',
                                    'cpp' => 'C++',
                                    'csharp' => 'C#',
                                    'dnetcore' => '.Net Core',
                                    'cloud' => 'Cloud computing (AWS, Azure, DigitalOcean,...)',
                                    'excel' => 'MS Excel, LibreOffice Calc',
                                    'ui' => 'UI/UX',
                                    'web' => 'Webdesign',
                                    'presta' => 'Prestashop',
                                    'wordp' => 'Wordpress',
                                    'vue' => 'Vue',
                                    'ang' => 'Angular',
                                    'react' => 'React',
                                ];
                                ?>
                                <table class="table mobile-friendly">
                                    <thead>
                                    <tr>
                                        <th scope="col" style="width: 25%"><b>Skill</b></th>
                                        <th scope="col" colspan="7"><b>Rate (0-nothing, 1-basic, 2-junior, 3-medior, 4-senior, 5-expert)</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($skills as $shorthand => $skill): ?>
                                        <tr>
                                            <td><?= $skill ?></td>

                                                <?php
                                                foreach (range(0,5) as $x):
                                                ?>
                                                <td>
                                                    <input type="radio" name="Quiz[skills][<?= $shorthand ?>][]" value="<?= $x ?>"> <?= $x ?>
                                                </td>
                                                <?php endforeach; ?>

                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-6">
                                <b>1. What will be the output of following php statement?</b>
                                <p style="margin-top:10px; margin-left: 10px;">
                                    echo (int) ((0.1 + 0.7) * 10);
                                    <br>
                                <ul style="list-style-type: none;">
                                    <li>
                                        <input
                                                type="radio"
                                                name="Quiz[q1]"
                                                value="7"
                                                <?= isset($data['q1']) && $data['q1'] == 7 ? ' checked' : '' ?>
                                        > 7</li>
                                    <li>
                                        <input
                                                type="radio"
                                                name="Quiz[q1]"
                                                value="6"
                                                <?= isset($data['q1']) && $data['q1'] == 6 ? ' checked' : '' ?>
                                        > 6
                                    </li>
                                    <li>
                                        <input
                                                type="radio"
                                                name="Quiz[q1]"
                                                value="8"
                                                <?= isset($data['q1']) && $data['q1'] == 8 ? ' checked' : '' ?>
                                        > 8
                                    </li>
                                    <li>
                                        <input
                                                type="radio"
                                                name="Quiz[q1]"
                                                value="0"
                                                <?= isset($data['q1']) && $data['q1'] == 0 ? ' checked' : '' ?>
                                        > 0
                                    </li>
                                </ul>
                                </p>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>2.  Will be output of this code text “Martin”? Please explain why yes or no.</b>
                                <p style="margin-top:10px;">
                                <pre>
                        abstract class Person
                        {
                            abstract public function getName();
                        }
                        class Customer extends Person
                        {
                            private $first_name = "Martin";
                            private $last_name = "Kod";

                            public function getFirstName() : string
                            {
                                return $this->first_name;
                            }
                        }
                        $customer = new Customer();
                        echo $customer->getFirstName();
                        </pre>
                                </p>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q2]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q2'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>3. The value of the variable input is a string 1,2,3,4,5,6,7. How would you get the sum of the
                                    integers contained inside input?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <input type="text" name="Quiz[q3]" class="form-control" value="<?= $data['q3'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>4. Explain this line of code:</b>
                                <p style="margin-top:10px; margin-left: 10px;">
                                    $message = 'Hello ' . ($user->get('first_name') ?: 'Guest');
                                </p>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q4]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q4'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>5. When will be this statement false, and when will be true?</b>
                                <p style="margin-top:10px; margin-left: 10px;">
                                <pre>
                        $a = ‘0’;

                        if( $a == 0 ) { … }
                        if( $a === 0 ) { … }
                        </pre>
                                </p>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q5]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q5'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>6. Write down the result of the next command:</b>
                                <pre style="margin-top: 10px;">
                        $string='[{"name":"PHP","Description":"Web notes"},{"name":"JSON"}]';
                        $data = json_decode($string);
                        print_r($data);
                    </pre>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q6]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q6'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>7. Write down example, how to handle exception.</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q7]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q7'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>8. Refactor this code, use Dependency injection</b>
                                <pre style="margin-top: 10px">
                        namespace Example;
                        class Client
                        {
                            public function execute()
                            {
                                $dependency = new Dependency();
                                $dependency->execute();
                            }
                        }
                    </pre>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q8]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q8'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>9. Write simple function which will check if number is Even or Odd</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q9]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q9'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>10. Write code which will calculate x! – factorial function.</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q10]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q10'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>11. What is the difference between the class and interface?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q11]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q11'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>12. Do you have experience with DDD, BDD, TDD? Describe the approach of DDD…</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q12]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q12'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-12">
                                <b>13. Please explain what is MVC?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q13]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q13'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>14. Does the PHP support multiple inheritance?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q14]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q14'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>15. In a PHP class what are the three visibility keywords of property or method?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q15]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q15'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>16. Please explain shortly what is Lazy Loading.</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q16]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q16'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>17. What is the meaning of a final class or final method?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q17]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q17'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>18. What is ORM, why we are using ORM?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q18]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q18'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>19. What is the execution plan? How would you view the execution plan?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q19]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q19'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>20. What types of JOIN do we have in MySQL, describe at least 3</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q20]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q20'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>21. Do you use Composer? If yes, what benefits have you found in it?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q21]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q21'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>22. Describe anonymous functions, describe anonymous class.</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q22]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q22'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>23. Which PSR standards are familiar to you?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q23]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q23'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>24. Do you know some design patterns? If yes, describe them</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q24]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q24'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>25. Docker – are you familiar with docker containers? How you can list containers in docker?</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q25]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q25'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>26. Do you know jsonrpc 2 format? What parts must be specified there. (jsonrpc, method, params, id)</b>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q26]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q26'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>27. What are the difference?</b>
                                <p style="margin-top:10px;">
                                <pre>
                                >>> level = [1,2,3]
                                >>> tier = (1,2,4)
                                </pre>
                                </p>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q27]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q27'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12">
                                <b>28. What will be the result?</b>
                                <p style="margin-top:10px;">
                                <pre>
                                >>> x = lambda a : a**2 + 10
                                >>> print(x(5))
                                </pre>
                                </p>
                                <p style="margin-top: 10px">Answer:</p>
                                <textarea name="Quiz[q28]" class="form-control" rows="5" style="margin-top: 10px;"><?= $data['q28'] ?? '' ?></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 40px">
                            <div class="col-sm-12" style="text-align: center">
                                <button type="submit" class="btn-sm">Finish</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php

$css = <<<CSS
#dev-test {
    width: 100%;
    height: auto;
    position: relative;
    clear: both;
}

#dev-test .dev-container {
    width: 60%;
    margin: 20px auto;
    position: relative;
}

/* --- QR between banner and form --- */
.qr-bridge{
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  /* Pull it slightly upward to visually sit between banner & fields */
  margin: -40px auto 20px;   /* tweak -40px if you want more/less overlap */
  width: 100%;
}

.qr-card{
  background: rgba(255,255,255,.98);
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,.15);
  padding: 14px 14px 8px;
  text-align: center;
  width: min(320px, 40vw);
}

.qr-card img{
  width: 100%;
  height: auto;
  display: block;
}

.qr-card small{
  display: block;
  font-size: 12px;
  color: #666;
  margin-top: 6px;
}

/* On small screens it’s not needed; hide for clarity */
@media (max-width: 992px){
  .qr-bridge{ display: none; }
}
CSS;
$this->registerCss($css);

$js = <<<JS
$('#dev-form').on('submit',function() {
    var x = true;
    
    $.each($('.re-data'), function(k,v) {
       if($(v).val() === '') {
           x &&= false;
       }
    });
   if (!x) {
       alert('Please fill all contact information!');
   } 
   return x;
});
JS;
$this->registerJs($js);
