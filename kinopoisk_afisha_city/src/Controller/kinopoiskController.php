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
        $returnARR = kinopoisk_afisha_city_xml_get();
        if(!$returnARR){
            return $output;
        }else {
            //
            foreach ($returnARR['SHOWING'] as $SHOWING) {
                if (!empty($SHOWING)) {
                    //
                    $table = array(
                        '#type' => 'table',
                        '#attributes' => array('id' => 'kinopoisk-block-modules-table', 'class' => ['kinopoisk-block-modules-table']),
                    );
                    $table["#header"] = array(
                        array('data' => ['#markup' => $SHOWING['DAY']], 'colspan' => 3),
                        //
                    );
                    $rowsArr = [];
                    // dpm($SHOWING);
                    foreach ($SHOWING['FILM'] as $code => $dt) {
                        $rowsArr[] = [
                            'date_name' => ['data' => ['#markup' => '<span class="date_name">' . $dt['NAME'] . '</span>']],
                            'date_age' => ['data' => ['#markup' => '<span class="date_age">' .(strlen($dt['AGE'])>0?$dt['AGE']."+":""). '</span>']],
                            'date_info' => ['data' => ['#markup' => '<span class="date_info">' . implode("<br>", $dt['INFO']) . '</span>']],
                        ];
                        if (count($dt['CINEMA'])) {
                            //
                            foreach ($dt['CINEMA'] as $code => $item) {
                                $rowsArr[] = [
                                    'date_cinema_name' => ['data' => ['#markup' => '<span class="date_cinema">' . $item['NAME'] . '</span>'], 'colspan' => 2],
                                    'date_cinema_session' => ['data' => ['#markup' => '<span class="date_session">' . $item['SESSION'] . '</span>']]
                                ];
                            }
                        }
                    }
                    $table["#rows"] = $rowsArr;
                    $content .= drupal_render($table);
                }
            }
            //
        }
        $content .= drupal_render($table);
        //
        $output['#title'] = $returnARR['CITY'];
        $output['#markup'] = $content;
        $output['#attached']['library'][] = 'kinopoisk_afisha_city/kinopoisk_afisha_city';
        return $output;
    }
}