<?php

/**
 * @file
 * Contains \Drupal\weather\Form\CollectWeatherSettings.
 */

namespace Drupal\weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class CollectWeatherSettings extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'collect_weather_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        // weather.collect_weather.settings.yml
        return [
            'weather.collect_weather.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Download our configs.
        $config = $this->config('weather.collect_weather.settings');
        // Add a field for the ability to set the default phone.
        // Next we will use this value in the previous form.
        $form['default_weather_number'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Default weather parsing'),
            '#default_value' => ($config->get('weather_number')?$config->get('weather_number'):"https://yandex.ru/pogoda/moscow/details"),
            '#description' => "https://yandex.ru/pogoda/moscow/details <br> https://yandex.ru/pogoda/saint-petersburg/details <br> https://yandex.ru/pogoda/yekaterinburg/details <br> https://yandex.ru/pogoda/novosibirsk/details <br> https://yandex.ru/pogoda/kaliningrad/details",
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
        $this->config('weather.collect_weather.settings')
            ->set('weather_number', $values['default_weather_number'])
            ->save();
    }
}