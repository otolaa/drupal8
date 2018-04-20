<?php

namespace Drupal\coinmarketcap\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Add a simple block with the text. Below is an abstract, it is also mandatory!!
 *
 * @Block(
 *   id = "coinmarketcap_block",
 *   subject = @Translation("coinmarketcap, parsing JSON"),
 *   admin_label = @Translation("CoinmarketcapBlock")
 * )
 */
class coinmarketcapBlock extends BlockBase {

    /* protected function blockAccess(AccountInterface $account) {
        We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIfHasPermission($account, 'administer blocks');
    } */

    /* Overrides \Drupal\Core\Block\BlockBase::defaultConfiguration(). */
    public function defaultConfiguration() {
        return array(
            'label' => t('Cryptocurrency'),
            //'content' => "",
            'cache' => array(
                'max_age' => 3600,
                'contexts' => array(
                    'cache_context.user.roles',
                ),
            ),
        );
    }

    /**/
    public function build() {
        $content = "";
        $vb_ = coinmarketcap_get_api();
        // dpm($vb_);
        // dpm(\Drupal::config('coinmarketcap.collect_coinmarketcap.settings')->get('coinmarketcap_number'));

        $header = array(
            array('data' => ['#markup' => ($vb_['date']?format_date($vb_['date'],"long"):"")], 'colspan' => 3),
        );
        $num_ = \Drupal::config('coinmarketcap.collect_coinmarketcap.settings')->get('coinmarketcap_number');
        $num_ = ($num_?($num_-1):(10-1));
        $rowsArr = [];
        if(!empty($vb_['currency'])){
            foreach ($vb_['currency'] as $k_=>$cur){
                if($k_ > $num_) continue;
                $rowsArrCur = [];
                foreach ($cur as $code=>$val_){
                    if(!in_array($code,["name","price_usd","percent_change_24h"])) continue;
                    switch ($code){
                        case "price_usd":
                            $val_ = '<span class="price_usd">$ '.$val_.'</span>';
                            break;
                        case "percent_change_24h":
                            $sc_ = ($val_>0?'positive_change':'negative_change');
                            $val_ = '<span class="'.$sc_.'">'.$val_.' %</span>';
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
                'id' => 'coinmarketcap-block-modules-table',
            ),
        );
        $content .= drupal_render($table);
        $content .= ($vb_['oll']?$vb_['oll']:"");
        $block = [
            '#type' => 'markup',
            '#markup' => $content,
        ];
        $block['#attached']['library'][] = 'coinmarketcap/coinmarketcap_css';
        return $block;
    }
}