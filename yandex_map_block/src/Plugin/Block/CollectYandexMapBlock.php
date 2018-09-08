<?php

namespace Drupal\yandex_map_block\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Add a simple block with the text. Below is an abstract, it is also mandatory!!
 *
 * @Block(
 *   id = "content_yandex_map_block",
 *   subject = @Translation("YandexMap, Block"),
 *   admin_label = @Translation("YandexMapBlock")
 * )
 */
class CollectYandexMapBlock extends BlockBase {

    /* protected function blockAccess(AccountInterface $account) {
        We display the block only to users who have the right of access 'administer blocks'.
        return AccessResult::allowedIfHasPermission($account, 'administer blocks');
    } */

    /* Overrides \Drupal\Core\Block\BlockBase::defaultConfiguration(). */
    public function defaultConfiguration() {
        return array(
            'label' => t('Yandex Map Block'),
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
        // Download our configs.
        $config = \Drupal::config('yandex_map_block.settings');
        $content = "";
        // create the form element
        $form['preview'] = [
            '#type' => 'item',
            //'#title' => $this->t('Address'),
            '#markup' => '<div id="yandex-map-block-preview" class="yandex-map-block-preview" data-height="'.($config->get('height_things')?$config->get('height_things'):400).'"></div>',
            '#prefix' => '<div id="yandex-map-block-widget" class="yandex-map-block-widget center">',
            '#suffix' => '</div>',
        ];
        $content .= drupal_render($form); // we call the form elements
        $block = [
            '#type' => 'markup',
            '#markup' => $content,
        ];
        $block['#title'] = $this->t("Address on the map");
        // we connect the library to the form and send the parameters to it
        $block['#attached'] = array(
            'library' => array('yandex_map_block/yandex_map_block'),
            'drupalSettings' => array(
                'yandex_map_block' => array(            // Название модуля
                    'yandex_map_block' => array(          // Название библиотеки
                        'balloonContent' => $config->get('balloonContent'),
                        'lat' => $config->get('lat_thing'),
                        'lng' => $config->get('lng_things'),
                        'zoom' => $config->get('zoom_things'),
                        'preset' => 'islands#darkBlueDotIcon',
                        'iconColor' => "#1d84c3",
                        'id' => 'yandex-map-block-preview',
                    )
                )
            ),
        );
        return $block;
    }
}