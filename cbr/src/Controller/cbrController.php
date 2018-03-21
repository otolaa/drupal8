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
        $vb_ = cbr_xml_get_cbr();
        $content .= "<div class='headers'>".$vb_['headers']."</div>";
        $header = array(
            array('data' => "Цифр. код"),
            array('data' => "Букв. код"),
            array('data' => "Единиц"),
            array('data' => "Валюта"),
            array('data' => "Курс"),
            //  array('data' => '↑↓'),
        );
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' =>  ($vb_['cbr']?$vb_['cbr']:""),
            '#attributes' => array(
                'id' => 'cbr-modules-table-content',
            ),
        );
        $content .= drupal_render($table);
        //
        $output = array();
        $output['#title'] = t('КУРС ВАЛЮТ CBR.RU');
        $output['#markup'] = $content;
        return $output;
    }
}