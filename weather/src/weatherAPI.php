<?php
namespace Drupal\weather;

class weatherAPI{
    public static function returnXML(){
        $weatherResult = NULL;
        if (!class_exists("phpQuery")) {
            require_once(dirname(__FILE__) . '/phpQuery/phpQuery.php');
        }
        $PARAM_PARSER = file_get_contents(\Drupal::config('weather.collect_weather.settings')->get('weather_number'));
        if($PARAM_PARSER ===  FALSE){
            return FALSE;
        }
        $doc = \phpQuery::newDocument($PARAM_PARSER);
        $weatherResult["HEADER"] = $doc->find('h1.title_level_1')->text();
        $weatherResult["CURRENT_WEATHER"] = $doc->find('h2.title_level_2')->text();

        $weather_body = $doc->find('div.forecast-details');
        $details__day = pq($weather_body)->find('dt.forecast-details__day');
        $details__day_info = pq($weather_body)->find('dd.forecast-details__day-info');

        $weatherResult['WEEK10'] = array();
        foreach($details__day as $i=>$el){
            $q = pq($el);
            $DAY_NUM = trim($q->find('strong.forecast-details__day-number')->text());
            $DAY_OF_WEEK = $q->find('span.forecast-details__day-month')->text() . ", " . $q->find('span.forecast-details__day-name')->text();
            if(strlen($DAY_NUM)>0 && strlen($DAY_OF_WEEK)>0) {
                $weatherResult['WEEK10']['DAY'][] = array(
                    "DAY_NUM" => $DAY_NUM,
                    "DAY_OF_WEEK" => $DAY_OF_WEEK,
                );
            }
        }

        foreach($details__day_info as $a=>$el){
            $q = pq($el);
            $weather_table_head_ = $q->find('thead.weather-table__head th.weather-table__head-cell');
            $HEADER_ = array();
            foreach ($weather_table_head_ as $wea) {
                $weaHtml = trim(pq($wea)->html());
                if (strlen($weaHtml) > 0) {
                    $HEADER_[] = $weaHtml;
                }
            }
            if(count($HEADER_)) {
                $weatherResult['WEEK10']['HEADER'][] = $HEADER_;
            }

            //
            $dops = array();
            $weather_table_row_ = $q->find('tbody.weather-table__body tr.weather-table__row');
            if(count($weather_table_row_)) {
                foreach ($weather_table_row_ as $d=>$row) {
                    $rowHtml = pq($row)->find('td.weather-table__body-cell');
                    foreach($rowHtml as $ds=>$rh){
                        $dops[$d][$ds] = pq($rh)->html();
                    }
                }
            }
            if(count($dops)) {
                $weatherResult['WEEK10']['DATA'][] = $dops;
            }
        }

        // FORECAST_BRIEF
        foreach($weatherResult['WEEK10']['DATA'] as $d_=>$dt){
            $weatherResult['FORECAST_BRIEF'][$d_]['DAY'] =  $weatherResult['WEEK10']['DAY'][$d_];
            $weatherResult['FORECAST_BRIEF'][$d_]['HEADER'] =  $weatherResult['WEEK10']['HEADER'][$d_];
            $weatherResult['FORECAST_BRIEF'][$d_]['DATA'] = $dt;
        }

        unset($weatherResult['WEEK10']);
        //
        return $weatherResult;
    }
}