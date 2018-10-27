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
                if (!in_array($cur[1], ['USD', 'EUR', 'UAH'])) continue;
                //
                $cur[4] = (float)str_replace(",", ".", $cur[4]);
                $difference = round($cur[4] - (float)str_replace(",", ".", $second['ITEMS'][$k][4]), 4);
                $cur[4] = ['data'=>['#markup'=>$cur[4].($difference>0?"↑":"↓")], 'class'=>'currency_description '.($difference>0?'UP':'DOWN').''];
                $cur[] = ['data'=>['#markup'=>($difference>0?"+":"").$difference], 'class'=>'currency_date '.($difference>0?'UP':'DOWN').''];
                $curArr[] = $cur;
            }
            foreach ($curArr as $v) {
                //
                $rows[] = [
                    'names'=> ['data' => ['#markup' => $v['1']], 'class'=>'currency_title'],
                    'date'=>$v['4'],
                    'rates'=>$v['5'],
                ];
            }
        }

        $header = array(
            array('data' => ['#markup' => 'КОД'], 'class'=>'currency_kod'),
            array('data' => ['#markup' => 'КУРС'], 'class'=>'currency_kod'),
            array('data' => ['#markup' => 'РАЗНИЦА'], 'class'=>'currency_kod'),
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