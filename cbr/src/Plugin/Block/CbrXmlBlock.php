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
            'label' => t('КУРС ВАЛЮТ'),
            //'content' => "CBR.RU - currency",
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
        $vb_ = cbr_xml_get_cbr();

        $header = array(
            array('data' => ['#markup' => '<span class="currency_kod">КОД</span>'],),
            array('data' => ['#markup' => '<span class="currency_kod">ДАТА</span>'],),
            array('data' => ['#markup' => '<span class="currency_kod">КУРС</span>'],),
        );

        /**/
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' =>  ($vb_['cbr_block']?$vb_['cbr_block']:""),
            '#attributes' => array(
                'id' => 'cbr-block-modules-table',
            ),
        );
        $content .= drupal_render($table);
        $content .= ($vb_['oll']?$vb_['oll']:"");
        $block = [
            '#type' => 'markup',
            '#markup' => $content,
        ];
        $block['#attached']['library'][] = 'cbr/cbr_xml';
        return $block;
    }
}