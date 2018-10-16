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
        $WXG = weather_xml_get();
        //dpm($WXG);
        if(!$WXG){
            return $output;
        }else{
            $content .= $WXG['HEADER'];
            //
            if(count($WXG['WEEK_TABLE'])) {
                $table = array(
                    '#type' => 'table',
                    '#attributes' => array('id' => 'weather-controller-modules-table', 'class' => ['weather-controller-modules-table']),
                );
                foreach ($WXG['WEEK_TABLE'] as $t => $DA) {
                    //
                    $header = array(
                        array('data' => ['#markup' => $DA['DAY']], 'colspan' => 3, 'class'=>'weather-header')
                    );
                    foreach ($WXG['HEADER_TABLE'] as $hdr) {
                        $header[] = array('data' => ['#markup' => $hdr], 'class'=>'weather-header');
                    }
                    $table["#rows"][] = $header;
                    foreach ($DA['ITEMS'] as $DT) {
                        $r = array();
                        foreach ($DT as $code => $dArr) {
                            $dArr = ($code == 1 ? '<i class="' . $dArr . '"></i>' : $dArr);
                            $r['date_' . $code] = ['data' => ['#markup' => is_array($dArr) ? implode("<br>", $dArr) : $dArr]];
                        }
                        $table["#rows"][] = $r;
                    }
                    //
                }
                $content .= drupal_render($table);
            }
        }
        //
        $output['#title'] = $WXG['HEADER_CURRENT'];
        $output['#markup'] = $content;
        return $output;
    }
}