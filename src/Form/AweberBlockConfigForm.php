<?php

namespace Drupal\aweber_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\aweber_block\AweberScopes;

/**
 * Class AweberBlockConfigForm.
 */
class AweberBlockConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'aweber_block.aweberblockconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aweber_block_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('aweber_block.aweberblockconfig');
    $redirectUri = $config->get('redirect_uri');

    $form['aweber'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General Aweber Settings'),
    ];
    $form['aweber']['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Aweber base url'),
      '#default_value' => $config->get('base_url'),
      '#required' => TRUE,
    ];
    $form['aweber']['client_id'] = [
    // To be changed to password.
      '#type' => 'textfield',
      '#title' => $this->t('Aweber Client ID'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE,
    ];
    $form['aweber']['client_secret'] = [
    // To be changed to password.
      '#type' => 'textfield',
      '#title' => $this->t('Aweber Client secret'),
      '#default_value' => $config->get('client_secret'),
      '#required' => TRUE,
    ];
    $form['aweber']['redirect_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect uri'),
      '#default_value' => isset($redirectUri) ? $redirectUri : $this->getRequest()->getSchemeAndHttpHost() . '/aweber_block/getCode',
      '#required' => TRUE,
    ];
    $form['aweber']['auth_request_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Auth request url'),
      '#default_value' => $config->get('auth_request_url'),
      '#required' => TRUE,
    ];
    $form['aweber']['auth_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Aweber Auth Token'),
      '#default_value' => $config->get('auth_token'),
      // '#required' => TRUE,.
    ];

    $form['aweber']['scopes'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Application scopes'),
      '#options' => AweberScopes::SCOPES,
      '#default_value' => $config->get('scopes'),
      '#description' => $this->t('The list of scopes to allow for the customer\'s account'),
      '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('aweber_block.aweberblockconfig')
      ->set('base_url', $form_state->getValue('base_url'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('redirect_uri', $form_state->getValue('redirect_uri'))
      ->set('auth_request_url', $form_state->getValue('auth_request_url'))
      ->set('auth_token', $form_state->getValue('auth_token'))
      ->set('scopes', $form_state->getValue('scopes'))
      ->save();
  }
}
