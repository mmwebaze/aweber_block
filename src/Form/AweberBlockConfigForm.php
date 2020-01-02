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
    global $base_root;
    $config = $this->config('aweber_block.aweberblockconfig');
    $redirectUri = $config->get('redirect_uri');
    $enable_redirect = $config->get('enable_redirect');
    $redirect_ink = $config->get('redirect_registration');

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
    $form['aweber_block_redirect'] = array(
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => t('Redirect Settings.'),
      '#tree' => TRUE,
    );
    $form['aweber_block_redirect']['enable_redirect']= array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable redirect on registration'),
      '#default_value' => isset($enable_redirect) ? $enable_redirect : FALSE,
    );
    $form['aweber_block_redirect']['redirect_link'] = [
      '#type' => 'url',
      '#title' => t('Page user is redirected to after registration.'),
      '#default_value' => isset($redirect_ink) ? $redirect_ink : $base_root.'/aweber_block/thankyou',
      '#description' => $this->t('Only internal drupal pages are supported.')
    ];

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
      ->set('enable_redirect', $form_state->getValue('aweber_block_redirect')['enable_redirect'])
      ->set('redirect_link', $form_state->getValue('aweber_block_redirect')['redirect_link'])
      ->save();
  }
}
