<?php
namespace Drupal\kinopoisk_afisha_city;
//

class kacAPI{
    public static function returnPageParsing(){
        $afisha_city_ = \Drupal::config('kinopoisk_afisha_city.collect_kinopoisk.settings')->get('afisha_city');
        $afisha_city_ = ($afisha_city_?$afisha_city_:1);
        return "https://www.kinopoisk.ru/afisha/city/".$afisha_city_."/";
    }
    // убирает атрибуты у тегов
    public static function pr($text){
        //<b><i><u>
        $text = strip_tags($text,"<b><i><u>");
        $text = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $text);
        return $text;
    }
    // получаем возраст для фильма
    public static function age($text){
        $text = trim(substr($text, -5));
        $text = str_replace("age", "", $text);
        return $text;
    }
    public static function info($text){
        $res = [];
        foreach (explode("</li>",$text) as $t){
            $res[] = trim(strip_tags($t));
        }
        $res = array_filter($res, 'strlen');
        return $res;
    }
    public static function returnXML(){
        $resArr = NULL;
        if (!class_exists("phpQuery")) {
            require_once(dirname(__FILE__) . '/phpQuery/phpQuery.php');
        }
        $PARAM_PARSER = file_get_contents(self::returnPageParsing());
        if($PARAM_PARSER ===  FALSE){
            return FALSE;
        }
        $doc = \phpQuery::newDocumentHTML($PARAM_PARSER,'utf-8');
        $resArr["CITY"] = $doc->find('td[colspan="3"] a.all')->text();

        // the showing
        $showing = $doc->find('div.showing');
        foreach ($showing as $i=>$el){
            $q = pq($el);
            $FILM_ = $q->find('div.films_metro');
            $FILM_ARR = [];
            foreach ($FILM_ as $f=>$fil){
                $qf = pq($fil);
                $timeArr = [];
                $space = $qf->find('div.showing_section dl');
                foreach ($space as $tm){
                    $time = pq($tm);
                    $timeArr[] = ["NAME"=>trim($time->find('dt.name')->text()),"SESSION"=>self::pr($time->find('dd.time')->html())];
                }
                //
                $FILM_NAME = trim($qf->find('div[class="title _FILM_"] div p a')->text());
                $FILM_INFO = self::info($qf->find('div[class="title _FILM_"] ul.film_info')->html());
                //$FILM_INFO_FIRST = trim($qf->find('div[class="title _FILM_"] ul.film_info li.film_info_first')->text());
                //$FILM_INFO_TIME = trim($qf->find('div[class="title _FILM_"] ul.film_info li span')->text());
                $FILM_AGE= self::age($qf->find('div[class="title _FILM_"] div p span')->attr('class'));
                $FILM_ARR[] = ["NAME"=>$FILM_NAME, "AGE"=>$FILM_AGE, "INFO"=>$FILM_INFO, "CINEMA"=>$timeArr];
            }
            $DAY_ = trim($q->find('div.showDate')->text());
            $resArr["SHOWING"][] = [
                "DAY"=>$DAY_,
                "FILM"=>$FILM_ARR,
            ];
        }
        //
        return $resArr;
    }
}