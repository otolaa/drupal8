<?php

namespace Drupal\weather\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Add a simple block with the text. Below is an abstract, it is also mandatory!!
 *
 * @Block(
 *   id = "weather_xml_block",
 *   subject = @Translation("yandex.ru/pogoda"),
 *   admin_label = @Translation("WeatherBlock")
 * )
 */
class WeatherBlock extends BlockBase {

    /* protected function blockAccess(AccountInterface $account) {
        We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIfHasPermission($account, 'administer blocks');
    } */

    /* Overrides \Drupal\Core\Block\BlockBase::defaultConfiguration(). */
    public function defaultConfiguration() {
        return array(
            'label' => t('Weather'),
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
        //
        $block = array();
        $block['#type'] = 'markup';
        $block['#attached']['library'][] = 'weather/weather';
        //
        $WXG = weather_xml_get();
        if(!$WXG){
            return $block;
        }else {
            if(!empty($WXG['WEEK_TABLE'][0])){
                // header construction
                $DA = $WXG['WEEK_TABLE'][0];
                $table = array(
                    '#type' => 'table',
                    '#attributes' => array('id' => 'weather-block-modules-table', 'class' => ['weather-block-modules-table']),
                );
                $table["#header"] = array(
                    array('data' => ['#markup' => $DA['DAY']], 'colspan' => 3),
                    //
                );
                foreach ($DA['ITEMS'] as $DT) {
                    $r = [];
                    $r['date_0'] = ['data' => ['#markup' => implode("<br>", $DT[0])]];
                    $r['date_1'] = ['data' => ['#markup' => '<i class="'.$DT[1].'"></i>']];
                    $r['date_2'] = ['data' => ['#markup' => $DT[2]]];
                    $table["#rows"][] = $r;
                }
                $content .= drupal_render($table);
            }
            $content .= "<div class='weather-url-pages'><a href='/weather'>" . $WXG['HEADER'] . "</a></div>";
            //
        }
        //
        $block['#title'] = $WXG['HEADER_CURRENT'];
        $block['#markup'] = $content;
        return $block;
    }
}