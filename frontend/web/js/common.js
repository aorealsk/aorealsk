"use strict";

$.fn.isOnScreen = function(){

    var win = $(window);

    var viewport = {
        top : win.scrollTop(),
        left : win.scrollLeft()
    };
    viewport.right = viewport.left + win.width();
    viewport.bottom = viewport.top + win.height();

    var bounds = this.offset();
    bounds.right = bounds.left + this.outerWidth();
    bounds.bottom = bounds.top + this.outerHeight();

    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

};

$(document).ready(function() {
	filterPrice();
    filterSize();

    $(window).scroll(function(){
		if($('.fact-counter').length){
			if ($('.fact-counter').isOnScreen()) {
				factCounter();
			}
		}
    });

    $('table').on('scroll', function () {
        $("table > *").width($("table").width() + $("table").scrollLeft());
    });
    
	// properties magnific popup
	$('#properties-magnific-popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		closeOnContentClick: false,
		closeBtnInside: false,
		mainClass: 'mfp-with-zoom mfp-img-mobile',
		image: {
			verticalFit: true,
			titleSrc: function(item) {
				return item.el.attr('title');
			}
		},
		gallery: {
			enabled: true
		},
		zoom: {
			enabled: true,
			duration: 300, // don't foget to change the duration also in CSS
			opener: function(element) {
				return element.find('img');
			}
		}
	});

    // Tabs
    $('.property-details .nav-tabs > li > a').on('click', function(e){
        e.preventDefault();
        var li = $(this).parent();
        var tabId = $(this).attr('href');

        if (!li.hasClass('active'))
        {
            $('.property-details .nav-tabs > li').removeClass('active');
            li.addClass('active');
            $('.property-details .tab-content .tab-pane').fadeOut('fast', function(){
                $(this).removeClass('active in');

                $('.property-details .tab-content '+ tabId +'.tab-pane').fadeIn('fast', function(){
                    $(this).addClass('active in');
                });
            });
        }
    });

    // Carousel
    $('.carousel-indicators li').on('click', function(){
        var data_target = $(this).data('target');
        var data_slideTo = $(this).data('slide-to');
        var carousel = $('.carousel-inner', $(this).parent());
        
        
        
    });

    $(document).on('change', '.filter-form select#filter-region, .filter-form select#filter-district', function(){
        var filterForm = $(this).parent();
        var resultField = $($(this).data('field'), filterForm);
        var filterID = $(this).val();
        var ajaxAction = $(this).data('action');

        $.ajax({
            method: "POST",
            url: 'ajax',
            dataType: 'json',
            data: {
                ajaxAction:ajaxAction,
                filter_id: filterID
            },
            beforeSend: function() {
                // START Preloader
            }
        }).done(function(json) {
            if (json.error != '')
                console.log(json.error);
            else
            {
                resultField.prop('disabled', true).find('option').not(':first').remove();
                if (ajaxAction == 'getDisctrictsByRegion')
                    $('#filter-town', filterForm).prop('disabled', true).find('option').not(':first').remove();

                $.each(json.list, function(index, element) {
                    resultField.append('<option value="' + element.id + '">' + element.name + '</option>');
                });

                // STOP Preloader
                if (json.list.length > 0)
                    resultField.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.sidebars-left .btn-search, .exclusive-menu .btn-estate-buy', function(){
        var filterBlock = ($(this).hasClass('btn-search')) ? $('.filter-block', $(this).parent()) : $('.sidebars-left .filter-block');
        var dataFilterType = $(this).data('filter-type');

        filterBlock.addClass('fadeInLeft').show();

        // Filter HTML creation
        var filterSidebar = $('.section-filter');
        var filterPopup = $('.filter-popup-container');
        
        setTimeout(function(){
            $('.slider.slider-horizontal', filterSidebar).remove();
            filterPopup.html(filterSidebar.html()).show();
            filterSidebar.empty();
            //filterPopup.show();
            filterPrice();
            filterSize();
            
            $('.js-filter-form-select').select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
            
            $("#filter-city").select2({
                placeholder: "Vyberte mesto",
                minimumInputLength: 3,
                language: {
                    inputTooShort: function () {
                        return "Zadajte 3 alebo viac znakov...";
                    }
                },
                ajax : {
                    method: "POST",
                    url: 'ajax',
                    dataType: 'json',                 
                    delay: 250,
                    data: function(params){
                        return {
                            ajaxAction:'getTownsByChar',
                            q: params.term
                        }
                    },
                    processResults: function(data,params) {
                        console.log(data);
                        return {
                            results: $.map( data.list, function(val,ind){ return {id: ind, text: val};})   
                        };
                    }
                }
            });
            
        }, 750);

        $(document).on('click', '.filter-block .filter-panel__close', function(){
            filterBlock.removeClass('fadeInLeft').addClass('fadeOutLeft');

            $('.slider.slider-horizontal', filterPopup).remove();
            filterSidebar.html(filterPopup.html());

            setTimeout(function(){
                filterBlock.removeClass('fadeOutLeft').hide();
                // Filter HTML removing
                filterPopup.hide();
                filterPrice();
                filterSize();
            }, 250);
        });
    });

    $('.size-filter-block #min-slider-value-size, .size-filter-block #max-slider-value-size').on('click', function(){
        var number_min = $(this).data('number-min');
        var number_max = $(this).data('number-max');
        
        
        console.log(number_min + '-' + number_max);
    });

    var gMapStyles = [{
              "featureType": "water",
              "elementType": "geometry",
              "stylers": [{
                "color": "#e9e9e9"
              }, {
                "lightness": 17
              }]
            }, {
              "featureType": "landscape",
              "elementType": "geometry",
              "stylers": [{
                "color": "#f5f5f5"
              }, {
                "lightness": 20
              }]
            }, {
              "featureType": "road.highway",
              "elementType": "geometry.fill",
              "stylers": [{
                "color": "#ffffff"
              }, {
                "lightness": 17
              }]
            }, {
              "featureType": "road.highway",
              "elementType": "geometry.stroke",
              "stylers": [{
                "color": "#ffffff"
              }, {
                "lightness": 29
              }, {
                "weight": 0.2
              }]
            }, {
              "featureType": "road.arterial",
              "elementType": "geometry",
              "stylers": [{
                "color": "#ffffff"
              }, {
                "lightness": 18
              }]
            }, {
              "featureType": "road.local",
              "elementType": "geometry",
              "stylers": [{
                "color": "#ffffff"
              }, {
                "lightness": 16
              }]
            }, {
              "featureType": "poi",
              "elementType": "geometry",
              "stylers": [{
                "color": "#f5f5f5"
              }, {
                "lightness": 21
              }]
            }, {
              "featureType": "poi.park",
              "elementType": "geometry",
              "stylers": [{
                "color": "#dedede"
              }, {
                "lightness": 21
              }]
            }, {
              "elementType": "labels.text.stroke",
              "stylers": [{
                "visibility": "on"
              }, {
                "color": "#ffffff"
              }, {
                "lightness": 16
              }]
            }, {
              "elementType": "labels.text.fill",
              "stylers": [{
                "saturation": 36
              }, {
                "color": "#333333"
              }, {
                "lightness": 40
              }]
            }, {
              "elementType": "labels.icon",
              "stylers": [{
                "visibility": "on"
              }]
            }, {
              "featureType": "transit",
              "elementType": "geometry",
              "stylers": [{
                "color": "#f2f2f2"
              }, {
                "lightness": 19
              }]
            }, {
              "featureType": "administrative",
              "elementType": "geometry.fill",
              "stylers": [{
                "color": "#fefefe"
              }, {
                "lightness": 20
              }]
            }, {
              "featureType": "administrative",
              "elementType": "geometry.stroke",
              "stylers": [{
                "color": "#fefefe"
              }, {
                "lightness": 17
              }, {
                "weight": 1.2
              }]
            }];

    if ($('#PropertyMap').length)
    {
        var propertyLatLng = { lat: gmap_latitude, lng: gmap_longitude };
        const map = new google.maps.Map(document.getElementById('PropertyMap'), {
                zoom: 14,
                center: propertyLatLng,
                mapTypeId: 'terrain',
                styles: gMapStyles,
                mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
            }
        );

        new google.maps.Marker({position: propertyLatLng, map});
    }
    if ($('#PropertiesMap').length)
    {
        map = new google.maps.Map(document.getElementById('PropertiesMap'), {
			center: new google.maps.LatLng(gmap_latitude, gmap_longitude),
			zoom: 12,
			mapTypeId: 'terrain',
			mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
            styles: gMapStyles
		});
		infoWindow = new google.maps.InfoWindow({minWidth: 500, maxWidth: '100%'});

		/*locationSelect = document.getElementById('locationSelect');
		locationSelect.onchange = function() {
			var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
			if (markerNum !== 'none')
			google.maps.event.trigger(markers[markerNum], 'click');
		};*/
        
        $.ajax({
            method: "POST",
            url: 'ajax',
            dataType: 'json',
            data: {
                ajaxAction: 'getPropertiesForMap',
                property_type: property_type
            },
            beforeSend: function() {
                // START Preloader
            }
        }).done(function(json) {
            if (json.error != '')
                console.log(json.error);
            else
            {
                var lats_total = 0;
                var lngs_total = 0;
                var items_total = 0;

                $.each(json.list, function(index, element) {
                    if (element.gps_lat != '' && element.gps_long != '')
                    {
                        var lat = parseFloat(element.gps_lat);
                        var lng = parseFloat(element.gps_long);
                        var latlng = new google.maps.LatLng(lat,lng);

                        var id_property = parseInt(element.id_property);
                        var price = element.price;
                        var image_url = (element.cover && element.cover.path ? element.cover.path : '/images/property-item1.jpg');
                        var category = element.prop_category_name;
                        var type = element.prop_type_name;
                        var title = element.title;
                        var rewrite_url = element.rewrite_url;
                        var is_exclusive = element.exclusive;
                        var total_results = element.total_results;
                        var properties_by_gps_html = element.properties_by_gps_html;
                        var bathrooms = element.features.pocet_kupelna;
                        var bedrooms = element.features.pocet_kuchyna;

                        if (is_exclusive == 1)
                        {
                            var address = element.address_full;
                        }
                        else
                        {
                            var address = element.mesto + ', ' + element.kraj;
                        }
                        
                        if (element.features)
                        {
                            if (element.features.plocha_celkova)
                            {
                                var plocha_title = 'Celková plocha';
                                var plocha_value = element.features.plocha_celkova;
                            }
                            else if (element.features.plocha_zastavana)
                            {
                                var plocha_title = 'Zastavaná plocha';
                                var plocha_value = '';
                            }
                            else if (element.features.plocha_uzitkova)
                            {
                                var plocha_title = 'Úžitková plocha';
                                var plocha_value = element.features.plocha_uzitkova;
                            }
                            else if (element.features.plocha_zahrada)
                            {
                                var plocha_title = 'Plocha záhrady';
                                var plocha_value = element.features.plocha_zahrada;
                            }
                            else 
                                var plocha_title = '';
                        }
                        else
                            var plocha_title = '';
                        

                        createMarker(latlng, lat, lng, id_property, price, address, image_url, category, type, title, rewrite_url, is_exclusive, total_results, properties_by_gps_html, plocha_value, bathrooms, bedrooms);

                        items_total++;

                        lats_total = parseFloat(lats_total + lat);
                        lngs_total = parseFloat(lngs_total + lng);
                    }
                });

                var lat_center = lats_total / items_total;
                var lng_center = lngs_total / items_total;

                const center = new google.maps.LatLng(lat_center, lng_center);
                //window.map.setZoom(17);
                // using global variable:
                window.map.panTo(center);
                
                //console.log(lat_center / items_total +' '+ lng_center / items_total);

                // STOP Preloader
                //if (json.list.length > 0)
                    //resultField.prop('disabled', false);
            }
        });
    }
});

function filterPrice()
{
    // price filtering logic
    if($("#filter-price").hasClass("show-values show-tooltip"))
    {
        $("#filter-price").slider({
            handle: 'square',
            tooltip: 'always',
            tooltip_split: false,
            tooltip_position: 'bottom',
            step: $(this).data('slider-step'),
            formatter: function formatter(val) {
                if (Array.isArray(val)) {
                    return val[0] + " - " + val[1];
                } else {
                    return val;
                }
            }
        });
        $(document).on('slide', '#filter-price', function(slideEvt){
        //$("#filter-price").on("slide", function(slideEvt) {
            var arr = slideEvt.value.toString();
            var minmax = arr.split(',');
            $("#min-slider-value").val(minmax[0]);
            $("#max-slider-value").val(minmax[1]);
        });
        $('#min-slider-value, #max-slider-value').keyup('input',function(){
            var minCurrentValue = parseFloat($('#min-slider-value').val());
            var maxCurrentValue = parseFloat($('#max-slider-value').val());
            var arr = [minCurrentValue, maxCurrentValue];
            $('#filter-price').slider('setAttribute', 'value', arr);
            $('#filter-price').slider('refresh');
        });
    }
    else
    {
        $("#filter-price").slider({
            handle: 'square',
            tooltip: 'always',
            tooltip_split: true,
            step: $(this).data('slider-step'),
        });
    } 
}

function filterSize()
{
    // size filtering logic
    if($("#filter-size").hasClass("show-values show-tooltip"))
    {
        $("#filter-size").slider({
            handle: 'square',
            tooltip: 'always',
            tooltip_split: false,
            tooltip_position: 'bottom',
            step: $(this).data('slider-step'),
            formatter: function formatter(val) {
                if (Array.isArray(val)) {
                    return val[0] + " - " + val[1];
                } else {
                    return val;
                }
            }
        });
        $(document).on('slide', '#filter-size', function(slideEvt){
        //$("#filter-size").on("slide", function(slideEvt) {
            var arr = slideEvt.value.toString();
            var minmax = arr.split(',');
            $("#min-slider-value-size").val(minmax[0]);
            $("#max-slider-value-size").val(minmax[1]);
        });
        $('#min-slider-value-size, #max-slider-value-size').keyup('input',function(){
            var minCurrentValue = parseFloat($('#min-slider-value-size').val());
            var maxCurrentValue = parseFloat($('#max-slider-value-size').val());
            var arr = [minCurrentValue, maxCurrentValue];
            $('#filter-size').slider('setAttribute', 'value', arr);
            $('#filter-size').slider('refresh');
        });
    }
    else
    {
        $("#filter-size").slider({
            handle: 'square',
            tooltip: 'always',
            tooltip_split: true,
            step: $(this).data('slider-step'),
        });
    } 
}

/* Google Maps */
function createMarker(latlng, lat, lng, id_property, price, address, image_url, category, type, title, rewrite_url, is_exclusive, total_results, properties_by_gps_html, plocha, bathrooms, bedrooms)
{
    var property_detail_url    = 'https://www.aoreal.sk/nehnutelnost/'+rewrite_url;
    var property_detail_button = '<div class="more-info"><a href="'+property_detail_url+'" class="btn">Zobraziť detaily <i class="fa fa-external-link" aria-hidden="true"></i></a></div>';
    
    var html_image     = '<div class="property-image"><img src="'+image_url+'" class="img-responsive" alt="'+title+'"></div>';
    var html_title     = '<h4>'+title+'</h4>';
	var html_detail    = (price ? '<p><i class="material-icons" title="Cena">payment</i> '+ (total_results > 1 ? 'od ' : '') + displayPrice(price) +'</p>' : '');
    var html_icons     = '<ul class="property-features"><li class="property-space">'+plocha+' m<sup>2</sup></li><li class="property-bathrooms">'+bathrooms+'</li><li class="property-bedrooms">'+bedrooms+'</li></ul>';

    if (is_exclusive == 1)
    {
        var gps_address   = '<p><i class="material-icons" title="Zemepisná šírka a dĺžka">navigation</i> '+lat.toFixed(6)+', '+lng.toFixed(6)+'</p>';
        var html_address  = '<p><i class="material-icons" title="Adresa">place</i> '+address+'</p>' + gps_address;
        var html_info     = property_detail_button;
    }
    else if (total_results == 1)
    {
        var gps_address   = '';
        var html_address  = '<p><i class="material-icons" title="Adresa">place</i> '+address+'</p>' + gps_address;
        var html_info     = property_detail_button;
    }
    else
    {
        var gps_address  = '';
        var html_address = '';
        var html_title   = '<h4>Počet ponúk: ' + total_results +'</h4>';
        var html_detail  = '<ul class="property-list">' + properties_by_gps_html + '</ul>';
        var html_info    = '<div class="more-info"><a href="#" class="btn">Zobraziť všetky <i class="fa fa-external-link" aria-hidden="true"></i></a></div>';
        var html_icons   = '';
    }

	var image = new google.maps.MarkerImage('https://www.aoreal.sk/images/icons/map-marker-red.png'); // https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png
	//var marker = new google.maps.Marker({ map: map, position: latlng }); // {map: map, icon: image, position: latlng}
    
    var marker = new MarkerWithLabel({
        position: latlng,
        map: map,
        icon: image,
        draggable: false,
        raiseOnDrag: true,
        labelContent: (is_exclusive == 1 ? '<i class="fa fa-star"></i>' : total_results),
        labelAnchor: new google.maps.Point(10, 52),
        labelClass: "labels", // the CSS class for the label
        //labelStyle: {opacity: 0.50},
        labelInBackground: false
    });

	google.maps.event.addListener(marker, 'mouseover', function() {
		infoWindow.setContent('<div class="row property-details"><div class="infowindow-close"></div><div class="col-xs-12 col col-sm-4 hidden-xs">' + html_image + '</div><div class="col-xs-12 col col-sm-8">' + html_title + html_detail + html_address + html_icons + '</div></div>' + html_info);
		infoWindow.open(map, marker);

		//google.maps.event.addListener(marker, 'click', function() {
            //window.location.href = (total_results > 1 ? '#' : 'https://www.aoreal.sk/nehnutelnost/' + rewrite_url);
            //infoWindow.close(map, marker);
        //});
        $('.infowindow-close').on('click', function(){
            infoWindow.close(map, marker);
        });
	});
    
    //google.maps.event.addListener(marker, 'mouseout', function() {
        //infoWindow.close(map, marker);
    //});

	//markers.push(marker);
	markers[id_property] = marker;
}

function parseXml(str)
{
	if (window.ActiveXObject)
	{
		var doc = new ActiveXObject('Microsoft.XMLDOM');
		doc.loadXML(str);
		return doc;
	}
	else if (window.DOMParser)
		return (new DOMParser()).parseFromString(str, 'text/xml');
}

function clearLocations(n)
{
	infoWindow.close();

    if (markers.length > 0)
    {
        markers.forEach(function(marker){
            marker.setMap(null);
        });
        markers.length = 0;
    }
}

function displayPrice(price)
{
    if (!price || price <= 0)
        return false;

    if (price > 10000)
        price = parseInt(price);

    price += '';
    var x = price.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2 + ' €';
}

// Fact Counter
function factCounter() {
    if($('.fact-counter').length){
        $('.fact-counter .counter-column.animated').each(function() {

            var $t = $(this),
                n = $t.find(".count-text").attr("data-stop"),
                r = parseInt($t.find(".count-text").attr("data-speed"), 10);

            if (!$t.hasClass("counted")) {
                $t.addClass("counted");
                $({
                    countNum: $t.find(".count-text").text()
                }).animate({
                    countNum: n
                }, {
                    duration: r,
                    easing: "linear",
                    step: function() {
                        $t.find(".count-text").text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $t.find(".count-text").text(this.countNum);
                    }
                });
            }

        });
    }
}