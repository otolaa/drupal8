<?php

namespace Drupal\kinopoisk_afisha_city\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * We declare our controller class.
 */
class kinopoiskController extends ControllerBase {

    /* public function access(AccountInterface $account) {
        // We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIf($account->hasPermission('do example things'));
    } */

    /**/
    public function kinopoiskXml(){
        $content = "";
        $output = [];
        $KAC = kac_xml_get();
        if(!$KAC){
            return $output;
        }else{
            //
            $table = array(
                '#type' => 'table',
                '#attributes' => array('id' => 'kinopoisk-block-modules-table', 'class' => ['kinopoisk-block-modules-table']),
            );
            $rowsArr = [];
            foreach ($KAC['SCHEDULE'] as $SHOWING):
            if(!empty($SHOWING)) {
                // dpm($SHOWING);
                $rowsArr[] = ['header'=>['data' => ['#markup' => $SHOWING['DATE']], 'colspan' => 3, 'class'=>'kino-header']];
                foreach ($SHOWING['FILM'] as $code => $dt) {
                    $rowsArr[] = [
                        'date_name' => ['data' => ['#markup' => $dt['TITLE']], 'class'=>'date_name'],
                        'date_age' => ['data' => ['#markup' => (strlen($dt['AGE'])>0?$dt['AGE']."+":"")], 'class'=>'date_age'],
                        'date_info' => ['data' => ['#markup' => implode("<br>", $dt['DESCRIPTION'])], 'class'=>'date_info'],
                    ];
                    if(!empty($dt['CINEMA'])): foreach ($dt['CINEMA'] as $CINEMA):
                        foreach ($CINEMA['TIME'] as $k=>$time):
                            $rowsArr[] = [
                                'date_cinema_name' => ['data' => ['#markup' => ($k==0?$CINEMA['TITLE']:"")], 'class'=>'date_cinema'],
                                'date_cinema_hall' => ['data' => ['#markup' => ($CINEMA['HALL'][$k]?$CINEMA['HALL'][$k]:"")], 'class'=>'date_hall'],
                                'date_cinema_session' => ['data' => ['#markup' => $time], 'class'=>'date_session'],
                            ];
                        endforeach;
                    endforeach; endif;
                }
            }
            endforeach;
            $table["#rows"] = $rowsArr;
            $content .= drupal_render($table);
            //
        }
        //
        $output['#title'] = $KAC['CITY'];
        $output['#markup'] = $content;
        $output['#attached']['library'][] = 'kinopoisk_afisha_city/kinopoisk_afisha_city';
        return $output;
    }
}