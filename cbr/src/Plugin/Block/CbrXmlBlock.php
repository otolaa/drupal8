<?php

namespace Drupal\cbr\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Add a simple block with the text. Below is an abstract, it is also mandatory!!
 *
 * @Block(
 *   id = "cbr_xml_block",
 *   subject = @Translation("currency, parsing XML"),
 *   admin_label = @Translation("CBR.RU")
 * )
 */
class CbrXmlBlock extends BlockBase {

    /* protected function blockAccess(AccountInterface $account) {
        We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIfHasPermission($account, 'administer blocks');
    } */

    /* Overrides \Drupal\Core\Block\BlockBase::defaultConfiguration(). */
    public function defaultConfiguration() {
        return array(
            'label' => $this->t('КУРС ВАЛЮТ'),
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
        $CBR = cbr_get();
        $fruit = array_shift($CBR);
        $second = array_shift($CBR);
        //
        $rows = [];
        $curArr = [];
        if(count($fruit['ITEMS'])) {
            foreach ($fruit['ITEMS'] as $k => &$cur) {
                if (!in_array($cur[1], ['USD', 'EUR', 'PLN'])) continue;
                //
                $cur[4] = (float)str_replace(",", ".", $cur[4]);
                $difference = round($cur[4] - (float)str_replace(",", ".", $second['ITEMS'][$k][4]), 4);
                $cur[4] = $cur[4].($difference>0?"↑":"↓");
                $cur[] = ($difference>0?"+":"").$difference;
                $curArr[] = $cur;
            }
            foreach ($curArr as $v) {
                //
                $rows[] = [
                    'names'=> ['data' => ['#markup' => '<span class="currency_title" title="'.$v['3'].'">'.$v['1'].' / RUB</span>']],
                    'date'=>['data' => ['#markup' => '<span class="currency_description">'.$v['4'].'</span>']],
                    'rates'=>['data' => ['#markup' => '<span class="currency_date">'.$v['5'].'</span>']]
                ];
            }
        }

        $header = array(
            array('data' => ['#markup' => '<span class="currency_kod">КОД</span>'],),
            array('data' => ['#markup' => '<span class="currency_kod">КУРС</span>'],),
            array('data' => ['#markup' => '<span class="currency_kod">РАЗНИЦА</span>'],),
        );
        //
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' =>  $rows?$rows:[],
            '#attributes' => array(
                'id' => 'cbr-block-modules-table',
            ),
        );
        $content .= drupal_render($table);
        $content .= "<div class='val'><a href='/cbrxml'>курс на ".($fruit['DATE']?$fruit['DATE']:'')."</a></div>";
        //
        $block = [
            '#type' => 'markup',
            '#markup' => $content,
        ];
        $block['#attached']['library'][] = 'cbr/cbr_xml';
        return $block;
    }
}