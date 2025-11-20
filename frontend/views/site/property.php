<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use frontend\models\Aoreal;

$this->title = 'Nehnuteľnosť';
$this->registerJsFile('//maps.google.com/maps/api/js?key=AIzaSyCQ0VxubqDu7tC48X0ktN_k0vF76DsTLFw', ['depends' => [yii\web\JqueryAsset::className()], 'position' => \yii\web\View::POS_HEAD]);
$this->params['breadcrumbs'][] = $this->title;
$this->params['class_body'] = 'property-details';

//$this->registerJsFile('js/bootstrap-slider.js', ['depends' => [yii\web\JqueryAsset::className()], 'position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('js/jquery.magnific-popup.min.js', ['depends' => [yii\web\JqueryAsset::className()], 'position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('js/imagesloaded.pkgd.js', ['depends' => [yii\web\JqueryAsset::className()], 'position' => \yii\web\View::POS_HEAD]);

//$this->registerCssFile('css/bootstrap-slider.css', ['depends' => [yii\bootstrap\BootstrapAsset::className()]]);
//$this->registerCssFile('css/magnific-popup.css', ['depends' => [yii\bootstrap\BootstrapAsset::className()]]);

?>
<main class="site-property-details">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url(/images/header-bg1.jpg);" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>			
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                    <strong><?= Html::encode($property_details['title']) ?></strong>				</h1>
                </div>
            </div>
        </div>
        <?= Alert::widget(); ?>
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
    <section class="section-wrapper">
        <div class="container">
            <div class="row">
                <!-- sidebar -->
                <aside class="sidebar col-md-3 col-md-push-9 col-sm-5 col-sm-push-7">
                    <?php /* echo Aoreal::htmlSearchBlock(); */ ?>
                    
                    <section class="section-agent-info">
                        <header class="section-heading">
                            <h2><?php echo Aoreal::trans('Kontaktujte makléra'); ?></h2>
                        </header>
                        <div class="section-content">
                            <!-- carousel agents -->
                            <div id="carousel-agents" class="carousel slide carousel-fade" data-ride="carousel">
                                <!-- slider content-->
                                <div class="carousel-inner">
                                    <!-- slider item 1 -->
                                    <div class="item active">
                                        <!-- agent 1 -->
                                        <div class="agent-details">
                                            <a href="https://www.aoreal.sk/makler-szabo-balazs" class="agent-img-wrapper">
                                                <span class="agent-avatar agent1" style="background-image: url('/images/agents/agent-1.jpg');">
                                                    <span class="dummy-element"></span>
                                                    <span class="border-rombous border-hover"></span>
                                                    <span class="dark-overlay"></span>
                                                    <span class="item-link"></span>
                                                </span>
                                            </a>
                                            <div class="agent-descr">
                                                <h3>Mgr. Balázs Szabó<span>maklér, konateľ</span></h3>
                                                <p>Hľadám riešenia na mieru pre každého jedného môjho klienta.</p>
                                            </div>
                                            <ul class="agent-contacts">
                                                <li><a href="tel:00421948009989"><i class="fa fa-phone"></i><br>+421 948 00 99 89</a></li>
                                            </ul>
                                            <img src="/images/separation-double-lines.png" alt="">
                                            <ul class="social-buttons">
                                                <li><a href="https://www.facebook.com/Aoreal-335956663895256/" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                                <li><a href="https://twitter.com/omega_real" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                                <?php /*<li><a href="#"><i class="fa fa-google-plus"></i></a></li>*/ ?>
                                                <li><a href="https://www.instagram.com/balazs.szabo.aoreal.consulting/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                                <li><a href="https://www.linkedin.com/company/alpha-omega-real-consulting-s-r-o/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php
                    /*
                    <!-- section agents in sidebar -->
                    <section class="section-agents">
                        <!-- section heading -->
                        <header class="section-heading">
                            <h2><?php echo Aoreal::trans('Naši makléri'); ?></h2>
                        </header><!-- //section heading -->
                        <!-- section content -->
                        <div class="section-content">
                            <!-- carousel agents -->
                            <div id="carousel-agents" class="carousel slide carousel-fade" data-ride="carousel">
                                <!-- slider content-->
                                <div class="carousel-inner">
                                    <!-- slider item 1 -->
                                    <div class="item active">
                                        <!-- agent 1 -->
                                        <div class="agent-details">
                                            <a href="agent-detail-fullwidth.html" class="agent-img-wrapper">
                                                <span class="agent-avatar agent1">
                                                    <span class="dummy-element"></span>
                                                    <span class="border-rombous border-hover"></span>
                                                    <span class="dark-overlay"></span>
                                                    <span class="item-link"></span>
                                                </span>
                                            </a>
                                            <div class="agent-descr">
                                                <h3>John Denn<span>CEO</span></h3>
                                                <p>Aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                            <img src="/images/separation-double-lines.png" alt="">
                                            <ul class="social-buttons">
                                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div><!-- //agent 1 -->
                                    </div><!-- //slider item 1 -->
                                    <!-- slider item 2 -->
                                    <div class="item">
                                        <!-- agent 2 -->
                                        <div class="agent-details">
                                            <a href="agent-detail-fullwidth.html" class="agent-img-wrapper">
                                                <span class="agent-avatar agent2">
                                                    <span class="dummy-element"></span>
                                                    <span class="border-rombous border-hover"></span>
                                                    <span class="dark-overlay"></span>
                                                    <span class="item-link"></span>
                                                </span>
                                            </a>
                                            <div class="agent-descr">
                                                <h3>Sara Ellet<span>Designer</span></h3>
                                                <p>Aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                            <img src="/images/separation-double-lines.png" alt="">
                                            <ul class="social-buttons">
                                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div><!-- //agent 2 -->
                                    </div><!-- //slider item 2 -->
                                    <!-- slider item 3 -->
                                    <div class="item">
                                        <!-- agent 3 -->
                                        <div class="agent-details">
                                            <a href="agent-detail-fullwidth.html" class="agent-img-wrapper">
                                                <span class="agent-avatar agent3">
                                                    <span class="dummy-element"></span>
                                                    <span class="border-rombous border-hover"></span>
                                                    <span class="dark-overlay"></span>
                                                    <span class="item-link"></span>
                                                </span>
                                            </a>
                                            <div class="agent-descr">
                                                <h3>Olga Delvy<span>Coder</span></h3>
                                                <p>Aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                            <img src="/images/separation-double-lines.png" alt="">
                                            <ul class="social-buttons">
                                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div><!-- //agent 3 -->
                                    </div><!-- slider item 3 -->
                                    <!-- slider item 4 -->
                                    <div class="item">
                                        <!-- agent 4 -->
                                        <div class="agent-details">
                                            <a href="#" class="agent-img-wrapper">
                                                <span class="agent-avatar agent4">
                                                    <span class="dummy-element"></span>
                                                    <span class="border-rombous border-hover"></span>
                                                    <span class="dark-overlay"></span>
                                                    <span class="item-link"></span>
                                                </span>
                                            </a>
                                            <div class="agent-descr">
                                                <h3>Megan Fox<span>Marketing</span></h3>
                                                <p>Aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                            <img src="/images/separation-double-lines.png" alt="">
                                            <ul class="social-buttons">
                                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div><!-- //agent 4 -->
                                    </div><!-- //slider item 4 -->
                                </div><!-- //slider content-->
                                <!-- pointers -->
                                <ol class="carousel-indicators">
                                    <li data-target="#carousel-agents" data-slide-to="0" class="active"></li>
                                    <li data-target="#carousel-agents" data-slide-to="1"></li>
                                    <li data-target="#carousel-agents" data-slide-to="2"></li>
                                    <li data-target="#carousel-agents" data-slide-to="3"></li>
                                </ol><!-- //pointers -->
                            </div><!-- //carousel agents -->
                        </div><!-- //section content -->
                    </section><!-- //section agents in sidebar -->
                    */ ?>
                    <?php
                    /*
                    <!-- banners in sidebar -->
                    <aside class="banners">
                        <div class="banner-wrapper banner1">
                            <a href="catalog-grid.html">
                                <h3>Best Sale Properties</h3>
                                <hr class="separator80px">
                                <p>Etiam elit felis, porta ut massa in, consectetur finibus nibh. Vestibulum in efficitur velit. Proin mollis est in risus faucibus, nec finibus mauris vehicula. Duis at eleifend dui.</p>
                            </a>
                            <a href="catalog-grid.html" class="btn"><?php echo Aoreal::trans('Viac detailov'); ?></a>
                            <span class="color-overlay"></span>
                            <span class="dark-overlay"></span>
                        </div>
                    </aside>
                    <!-- //banners in sidebar -->
                    */
                    ?>
                </aside><!-- //sidebar -->
                <!-- main content -->
                <?php
                // Get GPS data from address
                if (!empty($property_details['gps_lat']) && !empty($property_details['gps_long']))
                {
                    $dest_address = '';
                    // POI mentese
                    Aoreal::saveNearbyPlaces($property_details['id_property'], $property_details['gps_lat'], $property_details['gps_long']);
					Aoreal::savePoiDistanceByProperty($property_details['id_property'], $property_details['gps_lat'], $property_details['gps_long']);
                }
                elseif (empty($property_details['ulica']))
                {
                    $dest_address = $property_details['psc'].' '.$property_details['mesto'].', '.$property_details['kraj'].', '.$property_details['stat'];
                }
                else{
                    $dest_address = $property_details['address_full'];
                }
                if (!empty($dest_address))
                {
                    // CSERELNI KELL AZ API KULCSOT
                    $geocodeObject = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDdy0rot-7mCKdXkOYjoGtBn4ye50L1tv0&address='.urlencode($dest_address)), true);
                    if ($geocodeObject !== false)
                    {
                        // get latitude and longitude from geocode object
                        $latitude = $geocodeObject['results'][0]['geometry']['location']['lat'];
                        $longitude = $geocodeObject['results'][0]['geometry']['location']['lng'];

                        // GPS adatok mentese
                        Yii::$app->db->createCommand()->update('nehnutelnost', ['gps_lat' => $latitude, 'gps_long' => $longitude], 'id = '.$property_details['id'])->execute();
                    }
                }
                
                $nearbyPlaces = Aoreal::getNearbyPlaces($property_details['id_property'], false, '`type` ASC, `distance_by_car` ASC');
                
                if ($property_details['prop_type_id'] == 2)
                    $propTypeClass = 'for-rent';
                else
                    $propTypeClass = 'for-sale';

                $property_url = $property_details['rewrite_url'];
                $property_price = Aoreal::displayPrice($property_details['price']);
                $images = Aoreal::getImages($property_details['zmluva_id']);
                unset($images[0]); // Cover
                ?>
                <div class="col-md-9 col-md-pull-3 col-sm-7 col-sm-pull-5">
                    <!-- property details -->
                    <div class="property-details-content">
                        <!-- property images -->
                        <div id="properties-magnific-popup-gallery">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="<?php echo Aoreal::displayImage($property_details['cover']); ?>" class="property-image-wrapper" title="Living Room #1">
                                        <img src="<?php echo Aoreal::displayImage($property_details['cover']); ?>" alt="<?php echo $property_details['title']; ?>">
                                        <span class="property-price"><?php echo $property_price; ?></span>
                                        <span class="dark-overlay"></span>
                                        <span class="item-link"></span>
                                    </a>
                                </div>
                                <div class="property-thumbnails-wrapper clearfix">
                                    <?php
                                    foreach ($images as $image)
                                    {
                                        echo '
                                        <div class="col-md-3 col-sm-4 col-xs-4">
                                            <a href="'.$image['path'].'" class="property-thumbnail'.($image['aspect-ratio'] < 1.5 ? ' ar43' : ' default').'" title="'.$property_details['title'].'">
                                                <img src="'.$image['path'].'" alt="'.$property_details['title'].'">
                                                <span class="dark-overlay"></span>
                                                <span class="item-link"></span>
                                            </a>
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div><!-- //property images -->
                        <!-- property description -->
                        <div class="row">
                            <div class="col-md-12">
                                <article class="article-property-details">
                                    <?php
                                    if ($property_details['exclusive'] == 1)
                                    {
                                        echo '
                                        <p>'.$property_details['title'].' na adrese '.($property_details['street'] ? $property_details['street'] : '').', '.$property_details['psc'].' '.$property_details['mesto'].'</p>';
                                    }
                                    else
                                    {
                                        echo '
                                        <p>'.$property_details['title'].' v lokalite '.$property_details['mesto'].'</p>';
                                    }
                                    ?>
                                </article>
                            </div>
                        </div><!-- //property description -->
                        <!-- property tabs -->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- tabs control panel -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#panel-property-details"><?php echo Aoreal::trans('Podrobnosti'); ?></a></li>
                                    <?php /*<li><a data-toggle="tab" href="#panel-property-video"><?php echo Aoreal::trans('Video'); ?></a></li>*/ ?>
                                    <?php
                                    if ($property_details['exclusive'] == 1 && !empty($property_details['gps_lat']) && !empty($property_details['gps_long']))
                                        echo '<li><a data-toggle="tab" href="#panel-property-map">'.Aoreal::trans('Mapa').'</a></li>';
                                    ?>
                                    <?php
                                    if ($nearbyPlaces && sizeof($nearbyPlaces))
                                        echo '<li><a data-toggle="tab" href="#panel-property-nearby">'.Aoreal::trans('V blízkosti').'</a></li>';
                                    ?>
                                    <?php /*<li><a data-toggle="tab" href="#panel-property-agent"><?php echo Aoreal::trans('Maklér'); ?></a></li>*/ ?>
                                </ul><!-- //tabs control panel -->
                                <!-- tabs content -->
                                <div class="tab-content">
                                    <!-- tab property details -->
                                    <div id="panel-property-details" class="tab-pane in active">
                                        <table class="table table-striped table-property">
                                            <tbody>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Kategória'); ?>:</span> <span class="property-value"><?php echo $property_details['prop_category_name']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Typ'); ?>:</span> <span class="property-value"><?php echo 'Na '.mb_strtolower($property_details['prop_type_name']); ?></span></td>
                                                </tr>
                                                <?php
                                                if ($property_details['exclusive'] == 1)
                                                    echo '
                                                    <tr>
                                                        <td><span class="property-param">'.Aoreal::trans('Adresa').':</span> <span class="property-value">'.$property_details['address_full'].'</span></td>
                                                    </tr>';
                                                else
                                                    echo '
                                                    <tr>
                                                        <td><span class="property-param">'.Aoreal::trans('Lokalita').':</span> <span class="property-value">'.$property_details['mesto'].', '.$property_details['kraj'].'</span></td>
                                                    </tr>';
                                                ?>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Plocha'); ?>:</span> <span class="property-value">348 m<sup>2</sup></span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Spálne'); ?>:</span> <span class="property-value">5</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Kúpelne'); ?>:</span> <span class="property-value">3</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Klimatizácia'); ?>:</span> <span class="property-value">2</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Balkón'); ?>:</span> <span class="property-value">0</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="property-param"><?php echo Aoreal::trans('Internet'); ?>:</span> <span class="property-value">Yes (50 Mbit/s)</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- //tab property details -->
                                    <!-- tab property video -->
                                    <?php /*<div id="panel-property-video" class="tab-pane fade">
                                        <iframe class="embedded-video-frame" src="https://www.youtube.com/embed/twwbyMMvqtA" allowfullscreen>
                                        </iframe>
                                    </div><!-- //tab property video -->*/ ?>
                                    <?php if ($property_details['exclusive'] == 1 && !empty($property_details['gps_lat']) && !empty($property_details['gps_long'])) { ?>
                                    <!-- tab property map -->
                                    <div id="panel-property-map" class="tab-pane fade">
                                        <div class="google-map" id="PropertyMap" style="height: 500px;"></div>
                                        <script type="text/javascript">
                                            var gmap_latitude = <?php echo $property_details['gps_lat']; ?>;
                                            var gmap_longitude = <?php echo $property_details['gps_long']; ?>;
                                        </script>
                                    </div><!-- //tab property map -->
                                    <?php } ?>
                                    <?php
									
									$locationMaterialIcons = array(
										'grocery_or_supermarket'	=> array('name' => 'Potraviny', 'icon' => 'storefront', 'color' => ''),
										'bank'			=> array('name' => 'Banka', 'icon' => 'account_balance', 'color' => ''),
										'city_hall'		=> array('name' => 'Radnica', 'icon' => 'location_city', 'color' => ''),
										'gym'			=> array('name' => 'Fitness centrum', 'icon' => 'fitness_center', 'color' => ''),
										'hospital'		=> array('name' => 'Nemocnica', 'icon' => 'local_hospital', 'color' => ''),
										'museum'		=> array('name' => 'Múzeum', 'icon' => 'museum', 'color' => ''),
										'pharmacy'		=> array('name' => 'Lekáreň', 'icon' => 'local_pharmacy', 'color' => ''),
										'police'		=> array('name' => 'Polícia', 'icon' => 'local_police', 'color' => ''),
										'post_office'	=> array('name' => 'Pošta', 'icon' => 'local_post_office', 'color' => ''),
										'restaurant'	=> array('name' => 'Reštaurácia', 'icon' => 'restaurant', 'color' => ''),
										'school'		=> array('name' => 'Škola', 'icon' => 'school', 'color' => ''),
										'train_station'	=> array('name' => 'Železničná stanica', 'icon' => 'train', 'color' => '')
									);
									
                                    if ($nearbyPlaces && sizeof($nearbyPlaces))
                                    {
                                        echo '
                                        <div id="panel-property-nearby" class="tab-pane fade">';
                                        $place_radius = 0;
                                        $row_count = 0;
                                        foreach ($nearbyPlaces as $place)
                                        {
											if (!empty($place['type']) && isset($locationMaterialIcons[$place['type']]))
												$place_icon = '<i class="material-icons" title="'.$locationMaterialIcons[$place['type']]['name'].'">'.$locationMaterialIcons[$place['type']]['icon'].'</i>';
											else
												$place_icon = NULL;
											
                                            if ($row_count == 0)
                                                echo '
                                                <div class="place-radius-title">Nájdete do '.$place['radius'].' metrov <span>vzdialenosť</span></div>
                                                <ul>';
                                            elseif ($row_count > 0 && $place_radius != $place['radius'])
                                                echo '
                                                </ul>
                                                <div class="place-radius-title">Nájdete do '.$place['radius'].' metrov <span>vzdialenosť</span></div>
                                                <ul>';
                                            
                                            echo '<li class="badge-nearby">'.$place_icon.$place['name'].'<span>'.$place['distance_by_car'].' m</span></li>';
                                            $place_radius = $place['radius'];
                                            $row_count++;
                                            if ($row_count == count($nearbyPlaces))
                                                echo '</ul>';
                                        }
                                        echo '
                                        </div>';
                                    }
                                    ?>
                                    <?php /*
                                    <!-- tab property agent -->
                                    <div id="panel-property-agent" class="tab-pane fade">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="agent-details">
                                                    <div class="agent-img-wrapper">
                                                        <span class="agent-avatar agent2">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="agent-descr">
                                                    <h3>Sara Ellet</h3>
                                                    <p>In nec neque semper, commodo purus facilisis, vehicula magna. Etiam neque massa, tempor vitae tempus eu, pellentesque nec velit. Duis quis elementum lacus. Etiam molestie nisl est, in sagittis quam suscipit at. 
                                                    </p>
                                                    <h4>Contacts:</h4>
                                                    <ul class="contact-info contact-list">
                                                        <li class="contact-phone-number"><i class="fa fa-phone"></i><a href="#">1-800-123-4567</a></li>
                                                        <li class="contact-email"><i class="fa fa-envelope-o"></i><a href="#">info@elysium-real-estate.com</a></li>
                                                        <li class="contact-address"><i class="fa fa-map-marker"></i><a href="#">Elysium St, Los Angeles, EL 453018</a></li>
                                                    </ul>
                                                    <h4>Social Media Agent:</h4>
                                                    <ul class="social-buttons">
                                                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- //tab property agent -->
                                    */ ?>
                                </div>
                            </div><!-- //tabs content -->
                        </div><!-- //property tabs -->
                        <!-- property social buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <span class="social-buttons-heading"><?php echo Aoreal::trans('Zdieľať:'); ?> </span>
                                <ul class="social-buttons">
                                    <li class="btn-facebook"><a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode(Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']).'/'.$property_url); ?>"><i class="fa fa-facebook"></i> Facebook</a></li>
                                    <li class="btn-twitter"><a href="https://twitter.com/share?url=<?php echo urlencode(Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']).'/'.$property_url).'&text='.urlencode($property_details['title']); ?>"><i class="fa fa-twitter"></i> Twitter</a></li>
                                    <?php /*<li><a href="#"><i class="fa fa-google-plus"></i></a></li>*/ ?>
                                    <?php /*<li><a href="#"><i class="fa fa-instagram"></i></a></li>*/ ?>
                                    <li class="btn-linkedin"><a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode(Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']).'/'.$property_url).'&title='.urlencode($property_details['title']); ?>"><i class="fa fa-linkedin"></i> LinkedIn</a></li>
									<li class="btn-whatsapp"><a href="whatsapp://send?text=<?php echo urlencode(Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']).'/'.$property_url); ?>"><i class="fa fa-whatsapp"></i> WhatsApp</a></li>
									<li class="btn-viber-share"><a href="viber://forward?text=<?php echo urlencode(Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']).'/'.$property_url); ?>">Zdieľať na Viber</a></li>
                                </ul>
                            </div>
                        </div><!-- //property social buttons -->
                    </div><!-- //property details -->
                    <!-- section RELATED PROPERTIES -->
                    <section class="section-properties properties-related color-bg">
                        <!-- section heading -->
                        <header class="section-heading text-center">
                            <div class="row">
                                <h2><?php echo Aoreal::trans('Súvisiace nehnuteľnosti'); ?></h2>
                                <img src="/images/separation-header.png" alt="">
                            </div>
                        </header><!-- //section heading -->
                        <!-- section content -->
                        <div class="section-content">
                            <div class="row">
                                <!-- item 1 -->
                                <div class="col-md-4 col-sm-6 col-xs-12 property-details for-sale">
                                    <a href="property-detail-fullwidth.html" class="property-image-wrapper">
                                        <img src="/images/property-item1.jpg" alt="Property 1">
                                        <span class="property-price">$ 980,000</span>
                                        <span class="dark-overlay"></span>
                                        <span class="item-link"></span>
                                    </a>
                                    <div class="property-descr">
                                        <h3>Elysium St, Los Angeles,<br>EL 453018<span>For Rent</span></h3>
                                        <p>Etiam elit felis, porta ut massa in, consectetur</p>
                                        <hr>
                                        <ul class="property-features">
                                            <li class="property-space">348<sup>2</sup></li>
                                            <li class="property-bathrooms">3</li>
                                            <li class="property-bedrooms">5</li>
                                        </ul>
                                    </div>
                                </div><!-- //item 1 -->
                                <!-- item 2 -->
                                <div class="col-md-4 col-sm-6 col-xs-12 property-details for-sale">
                                    <a href="property-detail-fullwidth.html" class="property-image-wrapper">
                                        <img src="/images/property-item2.jpg" alt="Property 2">
                                        <span class="property-price">$ 240/mo</span>
                                        <span class="dark-overlay"></span>
                                        <span class="item-link"></span>
                                    </a>
                                    <div class="property-descr">
                                        <h3>Nerima Sanam St, Tokyo,<br>TM 3020<span>For Rent</span></h3>
                                        <p>Etiam elit felis, porta ut massa in, consectetur</p>
                                        <hr>
                                        <ul class="property-features">
                                            <li class="property-space">145<sup>2</sup></li>
                                            <li class="property-bathrooms">1</li>
                                            <li class="property-bedrooms">3</li>
                                        </ul>
                                    </div>
                                </div><!-- //item 2 -->
                                <!-- item 3 -->
                                <div class="col-md-4 col-sm-6 col-xs-12 property-details for-rent">
                                    <a href="property-detail-fullwidth.html" class="property-image-wrapper">
                                        <img src="/images/property-item3.jpg" alt="Property 3">
                                        <span class="property-price">$ 420,000</span>
                                        <span class="dark-overlay"></span>
                                        <span class="item-link"></span>
                                    </a>
                                    <div class="property-descr">
                                        <h3>Wynwood St, Miami,<br>SW 223190<span>For Sale</span></h3>
                                        <p>Etiam elit felis, porta ut massa in, consectetur</p>
                                        <hr>
                                        <ul class="property-features">
                                            <li class="property-space">310<sup>2</sup></li>
                                            <li class="property-bathrooms">2</li>
                                            <li class="property-bedrooms">4</li>
                                        </ul>
                                    </div>
                                </div><!-- //item 3 -->
                            </div>
                        </div><!-- //section content -->
                    </section><!-- //section RELATED PROPERTIES -->
                </div><!-- //main content -->
            </div>
            
            
            
        </div>
    </section>
</main>
