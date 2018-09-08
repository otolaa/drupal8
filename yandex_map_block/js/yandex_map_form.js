(function ($, Drupal) {
  Drupal.behaviors.libraryYandexMapBlockBehavior = {
    attach: function (context, settings) {
      /**/
      $('#yandex-map-block-widget', context).once('libraryYandexMapBlockBehavior').each(function(index, item) {
          var $ymb = drupalSettings.yandex_map_block.yandex_map_form;
          var $div = $("#"+$ymb['id']);
          var $zoom = ($ymb['zoom']?$ymb['zoom']:11);
          $div.height($div.attr("data-height"));
          /*
          console.log($ymb);
          console.log($div.attr("data-height"));
            */
          ymaps.ready(init);
          function init() {
              // -- add myMap
              var myMap = new ymaps.Map($ymb['id'], {
                  center: [ $ymb['lat'] , $ymb['lng'] ],
                  zoom: $zoom,
                  controls: ['typeSelector', 'geolocationControl', 'fullscreenControl', 'zoomControl'],
              });
              // -- http://api.yandex.ru/maps/doc/jsapi/beta/ref/reference/control.SearchControl.xml
              searchControl = new ymaps.control.SearchControl({ options: { noPlacemark: true, } });
              myMap.controls.add(searchControl, { left: '40px', top: '10px' });
              // -- 0
              if($ymb['lat'] && $ymb['lng']){
                  myPlacemark = new ymaps.Placemark([ $ymb['lat'] , $ymb['lng'] ], {}, {draggable: true, preset: $ymb['preset'], iconColor: $ymb['iconColor'], });
                  myMap.geoObjects.add( myPlacemark );
              }
              //--1
              searchControl.events.add(["resultselect", "resultshow"], function (e) {
                  // http://api.yandex.ru/maps/doc/jsapi/beta/ref/reference/control.SearchControl.xml
                  coords = searchControl.getResultsArray()[e.get("index")].geometry.getCoordinates(); // получаем координаты
                  // send in input
                  savecoordinats();
              });  // -- searchControl.events.add("resultselect"
              //--2
              myMap.events.add('click', function (e) {
                  coords = e.get('coords');
                  // send in input
                  savecoordinats();
              });  //- myMap.events.add('click'
              //--3
              myMap.geoObjects.events.add('dragend', function (event) {
                  var thisPlacemark = event.get('target');
                  coords = thisPlacemark.geometry.getCoordinates();
                  savecoordinats();
              });  //-- dragend
              //--4
              myMap.events.add('boundschange', function (event) {
                  if (event.get('newZoom') != event.get('oldZoom')) {
                      $("input[name='zoom_things']").val( event.get('newZoom') );
                  }
              }); // -- boundschange

              //--save
              function savecoordinats (){
                  // -- remove ALL and add  Placemark
                  myMap.geoObjects.removeAll();
                  vzoom = myMap.getZoom();
                  myPlacemark = new ymaps.Placemark(coords, {}, {draggable: true, preset: $ymb['preset'], iconColor: $ymb['iconColor'],});
                  myMap.geoObjects.add( myPlacemark );
                  //--- record in input
                  $('#edit-lat-thing').val( coords[0].toPrecision() );
                  $('#edit-lng-things').val( coords[1].toPrecision() );
                  $('#edit-zoom-things').val( vzoom );
              }  //-- function savecoordinats()
          }
          return false;
      });
    }
  };
})(jQuery, Drupal);