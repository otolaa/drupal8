<?php

namespace Drupal\cbr\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * We declare our controller class.
 */
class cbrController extends ControllerBase {

    /* public function access(AccountInterface $account) {
        // We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIf($account->hasPermission('do example things'));
    } */

    /**/
    public function cbrXml() {
        $content = "";
        $content .= "";
        $CBR = cbr_get();
        $fruit = array_shift($CBR);
        $second = array_shift($CBR);
        foreach ($fruit['ITEMS'] as $k => &$cur) {
            //
            $cur[4] = (float)str_replace(",", ".", $cur[4]);
            $difference = round($cur[4] - (float)str_replace(",", ".", $second['ITEMS'][$k][4]), 4);
            $cur[4] = ['data'=>['#markup'=>$cur[4].($difference>0?"↑":"↓")], 'class'=>'currency_description '.($difference>0?'UP':'DOWN').''];
            $cur[] = ['data'=>['#markup'=>($difference>0?"+":"").$difference], 'class'=>'currency_date '.($difference>0?'UP':'DOWN').''];
        }
        $header = array(
            array('data' => "Цифр. код"),
            array('data' => "Букв. код"),
            array('data' => "Единиц"),
            array('data' => "Валюта"),
            array('data' => "Курс"),
            array('data' => '↑↓'),
        );
        $rows = $fruit['ITEMS'];
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' =>  $rows?$rows:[],
            '#attributes' => array(
                'id' => 'cbr-modules-table-content',
            ),
        );
        $content .= drupal_render($table);
        //
        $output = array();
        $output['#title'] = $this->t('КУРС ВАЛЮТ')." ".($fruit['DATE']?$fruit['DATE']:'');
        $output['#markup'] = $content;
        return $output;
    }
}