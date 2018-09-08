(function ($, Drupal) {
  Drupal.behaviors.libraryYandexMapBlockBehavior = {
    attach: function (context, settings) {
      /**/
      $('#yandex-map-block-widget', context).once('libraryYandexMapBlockBehavior').each(function(index, item) {
          var $ymb = drupalSettings.yandex_map_block.yandex_map_block;
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
                  myPlacemark = new ymaps.Placemark([ $ymb['lat'] , $ymb['lng'] ], { balloonContent: $ymb['balloonContent'] }, { draggable: false, preset: $ymb['preset'], iconColor: $ymb['iconColor'] });
                  myMap.geoObjects.add( myPlacemark );
              }
          }
          return false;
      });
    }
  };
})(jQuery, Drupal);