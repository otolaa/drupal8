<?php

namespace Drupal\coinmarketcap\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * We declare our controller class.
 */
class coinmarketcapController extends ControllerBase {

    /* public function access(AccountInterface $account) {
        // We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIf($account->hasPermission('do example things'));
    } */

    /**/
    public function coinmarketcapApi(){
        $content = "";
        $vb_ = coinmarketcap_get_api();
        // dpm($vb_['currency']);
        $header = array(
            array('data' => t("Name")),
            array('data' => t("Price")),
            array('data' => t("Volume (24h)")),
            array('data' => t("Market Cap")),
            array('data' => t("Circulating Supply")),
            array('data' => t("Change (24h)")),
            // array('data' => t("Last updated")),
        );
        $rowsArr = [];
        if(!empty($vb_['currency'])){
            foreach($vb_['currency'] as $cur){
                $rowsArrCur = [];
                foreach ($cur as $code=>$val_){
                    if(!in_array($code,["name","market_cap_usd","price_usd","24h_volume_usd","total_supply","percent_change_24h"])) continue;
                    switch ($code){
                        case "24h_volume_usd":
                        case "market_cap_usd":
                            $val_ = '<span class="price_all">$ '.$val_.'</span>';
                            break;
                        case "total_supply":
                            $val_ = '<span class="total_supply">'.$val_.' '.$cur["symbol"].'</span>';
                            break;
                        case "price_usd":
                            $val_ = '<span class="price_usd">$ '.$val_.'</span>';
                            break;
                        case "percent_change_24h":
                            $sc_ = ($val_>0?'positive_change':'negative_change');
                            $val_ = '<span class="'.$sc_.'">'.$val_.' %</span>';
                            break;
                        case "name":
                            $val_ = '<span class="nameCurrency">'.$val_.'</span>';
                            break;
                    }
                    $rowsArrCur['date_' . $code] = ['data' => ['#markup' => $val_]];
                }
                $rowsArr[] = $rowsArrCur;
            }
        }
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' =>  $rowsArr,
            '#attributes' => array(
                'id' => 'coinmarketcap-modules-table-content',
            ),
        );
        $content .= drupal_render($table);
        //
        $output = array();
        $output['#title'] = t('Cryptocurrency')." → ".date("d.m H:i",$vb_['date']);
        $output['#markup'] = $content;
        $output['#attached']['library'][] = 'coinmarketcap/coinmarketcap_css';
        return $output;
    }
}