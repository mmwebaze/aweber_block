<?php

namespace Drupal\aweber_block\Form;

use Drupal\aweber_block\Service\AweberServiceInterface;
use Drupal\aweber_block\SubscriberFieldPluginManager;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Config\ImmutableConfig;


class AweberForm extends FormBase {
  /**
   * The messenger service.
   *
   * @var MessengerInterface
   */
  protected $messenger;

  /**
   * The Aweber Manager Service.
   *
   * @var AweberServiceInterface
   */
  protected $aweberService;

  /**
   * The list registration redirect params.
   *
   * @var array
   */
  protected $redirectParams;

  /**
   * Array of registration lists and other fields such as first name e.tc.
   *
   * @var array
   */
  protected $lists;
  protected $subscriberFields;

  /**
   * AweberForm constructor.
   *
   * @param array $lists
   *   Email lists
   * @param ImmutableConfig $aweberConfig
   * @param MessengerInterface $messenger
   * @param AweberServiceInterface $aweberService
   */

  /**
   * @var SubscriberFieldPluginManager
   */
  protected $subscriberFieldPluginManager;

  public function __construct(array $lists, ImmutableConfig $aweberConfig, MessengerInterface $messenger, AweberServiceInterface $aweberService, SubscriberFieldPluginManager $subscriber_field_plugin_manager) {
    $this->lists = $lists;
    $this->messenger = $messenger;
    $this->aweberService = $aweberService;
    $this->subscriberFields = $aweberConfig->get('fields');
    $this->redirectParams['enable_redirect'] = $aweberConfig->get('enable_redirect');
    $this->redirectParams['redirect_link'] = $aweberConfig->get('redirect_link');
    $this->subscriberFieldPluginManager = $subscriber_field_plugin_manager;
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aweber_block_form';
  }

  /**
   * {@inheritdoc}
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function buildForm(array $form, FormStateInterface $form_state, $parameter = NULL) {

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#required' => TRUE,
    ];

    foreach ($this->subscriberFields as $subscriberField){
      if ($subscriberField){
        try{
          $instance = $this->subscriberFieldPluginManager->createInstance($subscriberField);
          $form[$subscriberField] = $instance->field();
        }
        catch (PluginException $pe){
          //@todo replace by using DI.
          \Drupal::logger('aweber_block')->error($pe->getMessage());
        }
      }
    }

    $form['email_lists'] = [
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#title' => t('Email Lists:'),
      '#options' => $this->lists,
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    $selectedLists = $form_state->getValue('email_lists');

    foreach ($this->subscriberFields as $subscriberField){
      if ($subscriberField){
        $params[$subscriberField] = $form_state->getValue($subscriberField);
      }
    }

    foreach ($selectedLists as $selectedList => $value) {
      if ($value != 0) {
        $params['email'] = $email;
        $isSubscribed = $this->aweberService->checkSubscriberExistsByEmail($email, $selectedList);
        if ($isSubscribed){

          $result = $this->aweberService->addSubscribers($selectedList, $params);

          if ($this->redirectParams['enable_redirect']){
            $url = Url::fromUri($this->redirectParams['redirect_link']);
            $form_state->setRedirectUrl($url);
          }
        }
      }
    }
  }
}
