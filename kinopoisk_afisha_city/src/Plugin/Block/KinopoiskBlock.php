<?php

namespace Drupal\kinopoisk_afisha_city\Plugin\Block;

use Drupal\kinopoisk_afisha_city\kacAPI;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Add a simple block with the text. Below is an abstract, it is also mandatory!!
 *
 * @Block(
 *   id = "kinopoisk_afisha_city",
 *   subject = @Translation("kinopoisk.ru/afisha/city/1/"),
 *   admin_label = @Translation("KinopoiskBlock")
 * )
 */
class KinopoiskBlock extends BlockBase {

    /* protected function blockAccess(AccountInterface $account) {
        We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIfHasPermission($account, 'administer blocks');
    } */

    /* Overrides \Drupal\Core\Block\BlockBase::defaultConfiguration(). */
    public function defaultConfiguration() {
        return array(
            'label' => $this->t('KinopoiskBlock'),
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
        //
        $block = array();
        $block['#type'] = 'markup';
        // $block['#attached']['library'][] = 'kinopoisk_afisha_city/kinopoisk_afisha_city';
        //
        $returnARR = kinopoisk_afisha_city_xml_get();
        if(!$returnARR){
            return $block;
        }else {
            $SHOWING = $returnARR['SHOWING'][0];
            if(!empty($SHOWING)){
                //
                $table = array(
                    '#type' => 'table',
                    '#attributes' => array('id' => 'kinopoisk-block-modules-table', 'class' => ['kinopoisk-block-modules-table']),
                );
                $table["#header"] = array(
                    array('data' => ['#markup' => $SHOWING['DAY']], 'colspan' => 4),
                    //
                );
                $rowsArr = [];
                foreach ($SHOWING['FILM'] as $code =>$dt) {
                   $rowsArr[] = [
                       'date_name'=> ['data' => ['#markup' => $dt['NAME']]],
                       'date_age'=> ['data' => ['#markup' => $dt['AGE']."+"]],
                       'date_info'=> ['data' => ['#markup' => substr($dt['INFO'][0],0,-1)]],
                       'date_time'=> ['data' => ['#markup' => $dt['INFO'][3]]],
                   ];
                }
                $table["#rows"] = $rowsArr;
                $content .= drupal_render($table);
            }
            $content .= "<div class='kinopoisk-url-pages'><a href='/kinopoisk'>" . $this->t('Все расписание') . "</a></div>";
            //
        }
        //
        $block['#title'] = $returnARR['CITY'];
        $block['#markup'] = $content;
        return $block;
    }
}