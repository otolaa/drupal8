<?php
namespace Drupal\kinopoisk_afisha_city;
//

class kacAPI{
    public static function returnPageParsing(){
        $afisha_city_ = \Drupal::config('kinopoisk_afisha_city.collect_kinopoisk.settings')->get('afisha_city');
        $afisha_city_ = ($afisha_city_?$afisha_city_:1);
        return "https://www.kinopoisk.ru/afisha/city/".$afisha_city_."/";
    }
    public static function kinoPageXpath()
    {
        $res = NULL;
        $PARAM_PARSER = file_get_contents(self::returnPageParsing());
        if($PARAM_PARSER ===  FALSE){
            return FALSE;
        }
        //
        $doc = new \DOMDocument();
        $doc->loadHTML($PARAM_PARSER); // from html
        $xpath = new \DOMXpath($doc);
        //
        $body = $xpath->query('//td[@id="block_left"]/div[@class="block_left"]/table/tr/td');
        $headers = $xpath->query('table/tr/td[@colspan="3"]/table/tr/td[@colspan="3"]/a',$body->item(0))->item(0)->nodeValue;
        //
        $filmArr = [];
        $dtFilm = $xpath->query('div[@class="showing"]',$body->item(0));
        foreach ($dtFilm as $fl) {
            //
            $showDate = $xpath->query('div[@class="showDate"]|div[@class="showDate gray"]',$fl)->item(0)->nodeValue;
            $fmArr = [];
            $fm = $xpath->query('div[@class="films_metro "]|div[@class="films_metro"]',$fl);
            foreach ($fm as $f){
                $fmn = $xpath->query('div[@class="title _FILM_"]/div/p/a',$f)->item(0)->nodeValue;
                $age = false;
                if($age = $xpath->query('div[@class="title _FILM_"]/div/p/span',$f)->item(0)):
                    $age = explode(" ",$age->getAttribute('class'));
                    $age = str_replace("age", "", $age[count($age)-1]);
                endif;
                // descriptions for films
                $fmInfoArr = [];
                $fmInfo = $xpath->query('div[@class="title _FILM_"]/ul/li',$f);
                foreach ($fmInfo as $fi){
                    $fmInfoArr[] = $fi->textContent;
                }
                // the cinema и т.д.
                $cinemaArr = [];
                $cnm = $xpath->query('div[@class="showing_section"]/dl',$f);
                foreach ($cnm as $cinema) {
                    $cnn = $xpath->query('dt[@class="name"]',$cinema)->item(0)->textContent; // название кинотеатра и т.д.
                    // the time
                    $shArr = [];
                    foreach ($xpath->query('dd[@class="time"]',$cinema) as $time){
                        $ibu = [];
                        foreach ($xpath->query('i|u|b',$time) as $tt){
                            $ibu[] = "<".$tt->tagName.">".trim($tt->textContent)."</".$tt->tagName.">";
                        }
                        $shArr[] = implode(" ",$ibu);
                    }
                    // hall
                    $hallArr = [];
                    foreach ($xpath->query('dd[@class="hall"]',$cinema) as $hall){
                        // return html
                        $hall = $body->item(0)->ownerDocument->saveHTML($hall);
                        $hall = strip_tags($hall,'<u>');
                        if(strlen($hall)>1):
                            if(strpos((string)$hall, "imax") === false){ /**/ }else{ $hall = "IMAX"; }
                            if(strpos((string)$hall, "3D") === false){ /**/ }else{ $hall = "3D"; }
                        endif;
                        // return html CSS и т.д.
                        $hallArr[] = $hall;
                    }
                    $cinemaArr[] = ['TITLE'=>$cnn,'TIME'=>$shArr,'HALL'=>$hallArr];
                }
                //
                $fmArr[] = ['TITLE'=>$fmn, 'AGE'=>$age, 'DESCRIPTION'=>$fmInfoArr, 'CINEMA'=>$cinemaArr,];
            }
            //
            $filmArr[] = ['DATE'=>$showDate,'FILM'=>$fmArr];
        } // end FILMS
        $res = ['CITY'=>$headers,'SCHEDULE'=>$filmArr];
        //
        return $res;
    }
}