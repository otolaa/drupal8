<?php

namespace Drupal\weather\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * We declare our controller class.
 */
class weatherController extends ControllerBase {

    /* public function access(AccountInterface $account) {
        // We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIf($account->hasPermission('do example things'));
    } */

    /**/
    public function weatherXml() {
        $content = "";
        $output = array();
        $output['#title'] = t('Weather');
        $output['#attached']['library'][] = 'weather/weather';
        $wxg_ = weather_xml_get();
        if(!$wxg_){
            return $output;
        }else{
            $content .= "<p>".$wxg_['HEADER']."</p>";
            //
            if(count($wxg_['FORECAST_BRIEF'])) {
                foreach ($wxg_['FORECAST_BRIEF'] as $idt => $dayArr_) {
                    // header construction
                    $table = array(
                        '#type' => 'table',
                        '#attributes' => array('id' => 'weather-controller-modules-table-' . $idt . '', 'class' => ['weather-controller-modules-table']),
                    );
                    $table["#header"] = array(
                        array('data' => ['#markup' => '<div class="weather-table__value">' . implode(" ", $dayArr_['DAY']) . '</div>'],),
                        array('data' => ''),
                        array('data' => ''),
                        //
                    );
                    foreach ($dayArr_['HEADER'] as $hdr) {
                        $table["#header"][] = array('data' => ['#markup' => $hdr]);
                    }
                    foreach ($dayArr_['DATA'] as $dt) {
                        $rowsArr = array();
                        foreach ($dt as $code => $dArr) {
                            $rowsArr['date_' . $code] = ['data' => ['#markup' => $dArr]];
                        }
                        $table["#rows"][] = $rowsArr;
                    }
                    $content .= drupal_render($table);
                }
            }
        }
        //
        $output['#title'] = $wxg_['CURRENT_WEATHER'];
        $output['#markup'] = $content;
        return $output;
    }
}