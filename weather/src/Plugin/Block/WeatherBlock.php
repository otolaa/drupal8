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
        $wxg_ = weather_xml_get();
        if(!$wxg_){
            return $block;
        }else {
            if(!empty($wxg_['FORECAST_BRIEF'][0])){
                // header construction
                $dayArr_ = $wxg_['FORECAST_BRIEF'][0];
                $table = array(
                    '#type' => 'table',
                    '#attributes' => array('id' => 'weather-block-modules-table', 'class' => ['weather-block-modules-table']),
                );
                $table["#header"] = array(
                    array('data' => ['#markup' => implode(" ", $dayArr_['DAY'])], 'colspan' => 3),
                    //
                );
                foreach ($dayArr_['DATA'] as $dt) {
                    $rowsArr = array();
                    foreach ($dt as $code => $dArr) {
                        if($code>2)continue;
                        $rowsArr['date_' . $code] = ['data' => ['#markup' => $dArr]];
                    }
                    $table["#rows"][] = $rowsArr;
                }
                $content .= drupal_render($table);
            }
            $content .= "<div class='weather-url-pages'><a href='/weather'>" . $wxg_['HEADER'] . "</a></div>";
            //
        }
        //
        $block['#title'] = $wxg_['CURRENT_WEATHER'];
        $block['#markup'] = $content;
        return $block;
    }
}