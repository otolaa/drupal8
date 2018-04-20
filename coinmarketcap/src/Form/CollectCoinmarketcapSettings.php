<?php

/**
 * @file
 * Contains \Drupal\coinmarketcap\Form\CollectCoinmarketcapSettings.
 */

namespace Drupal\coinmarketcap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class CollectCoinmarketcapSettings extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'collect_coinmarketcap_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        // coinmarketcap.collect_coinmarketcap.settings.yml
        return [
            'coinmarketcap.collect_coinmarketcap.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Download our configs.
        $config = $this->config('coinmarketcap.collect_coinmarketcap.settings');
        // Add a field for the ability to set the default phone.
        // Next we will use this value in the previous form.
        $descriptionText = t("This parameter displays the number of elements in the block");
        $form['default_coinmarketcap_number'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Default coinmarketcap parsing'),
            '#default_value' => ($config->get('coinmarketcap_number')?$config->get('coinmarketcap_number'):"10"),
            '#description' => $descriptionText,
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
        $this->config('coinmarketcap.collect_coinmarketcap.settings')
            ->set('coinmarketcap_number', $values['default_coinmarketcap_number'])
            ->save();
    }
}