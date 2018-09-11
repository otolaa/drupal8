<?php

/**
 * @file
 * Contains \Drupal\kinopoisk_afisha_city\Form\CollectKinopoiskSettings.
 */

namespace Drupal\kinopoisk_afisha_city\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class CollectKinopoiskSettings extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'collect_kinopoisk_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        // kinopoisk_afisha_city.collect_kinopoisk.settings.yml
        return [
            'kinopoisk_afisha_city.collect_kinopoisk.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Download our configs.
        $config = $this->config('kinopoisk_afisha_city.collect_kinopoisk.settings');
        // Add a field for the ability to set the default phone.
        // Next we will use this value in the previous form.
        $sityArr = [
            '1'=>'Москва',
            '2'=>'Санкт-Петербург',
            '5061'=>'Абакан',
            '6967'=>'Абдулино',
            '490'=>'Калининград',
        ];
        $form['default_afisha_city'] = array(
            '#type' => 'select',
            '#title' => $this->t('Default page parsing'),
            '#options' => $sityArr,
            '#default_value' => ($config->get('afisha_city')?$config->get('afisha_city'):Null),
            '#description' => $this->t('Parsing for example https://www.kinopoisk.ru/afisha/city/1/'),
        );
        // Subit is inherited from ConfigFormBase
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        // We write the values into our config file and save it.
        $this->config('kinopoisk_afisha_city.collect_kinopoisk.settings')
            ->set('afisha_city', $values['default_afisha_city'])
            ->save();
    }
}