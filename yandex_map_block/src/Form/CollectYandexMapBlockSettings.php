<?php

/**
 * @file
 * Contains \Drupal\yandex_map_block\Form\CollectYandexMapBlockSettings.
 */

namespace Drupal\yandex_map_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class CollectYandexMapBlockSettings extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'yandex_map_block_admin_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        // yandex_map_block.settings.yml
        return [
            'yandex_map_block.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Download our configs.
        $config = $this->config('yandex_map_block.settings');
        //
        $form['balloonContent'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address on the map'),
            '#default_value' => ($config->get('balloonContent')?$config->get('balloonContent'):""),
            '#description' => $this->t("write the address of your company"),
        );
        $form['lat_thing'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Latitude things'),
            '#default_value' => ($config->get('lat_thing')?$config->get('lat_thing'):55.765625),
        );
        $form['lng_things'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Longitude things'),
            '#default_value' => ($config->get('lng_things')?$config->get('lng_things'):37.710359),
        );
        $form['zoom_things'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Zoom things'),
            '#default_value' => ($config->get('zoom_things')?$config->get('zoom_things'):11),
        );
        $form['height_things'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Height things'),
            '#default_value' => ($config->get('height_things')?$config->get('height_things'):400),
        );
        $form['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Preview'),
            '#markup' => '<div id="yandex-map-block-preview" class="yandex-map-block-preview" data-height="'.($config->get('height_things')?$config->get('height_things'):400).'"></div>',
            '#prefix' => '<div id="yandex-map-block-widget" class="yandex-map-block-widget center">',
            '#suffix' => '</div>',
        ];
        // we connect the library to the form and send the parameters to it
        $form['#attached'] = array(
            'library' => array('yandex_map_block/yandex_map_form'),
            'drupalSettings' => array(
                'yandex_map_block' => array(            // Название модуля
                    'yandex_map_form' => array(          // Название библиотеки
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
        // Subit is inherited from ConfigFormBase
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Retrieve the configuration
        $this->configFactory->getEditable('yandex_map_block.settings')
            // Set the submitted configuration setting
            ->set('lat_thing', $form_state->getValue('lat_thing'))
            // You can set multiple configurations at once by making
            // multiple calls to set()
            ->set('lng_things', $form_state->getValue('lng_things'))
            ->set('height_things', $form_state->getValue('height_things'))
            ->set('zoom_things', $form_state->getValue('zoom_things'))
            ->set('balloonContent', $form_state->getValue('balloonContent'))
            ->save();
    }
}