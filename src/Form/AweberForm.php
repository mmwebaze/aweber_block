<?php

namespace Drupal\aweber_block\Form;

use Drupal\aweber_block\Service\AweberServiceInterface;
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
  private $fields = [];

  /**
   * AweberForm constructor.
   *
   * @param array $fields
   * @param ImmutableConfig $aweberConfig
   * @param MessengerInterface $messenger
   * @param AweberServiceInterface $aweberService
   */
  public function __construct(array $fields, ImmutableConfig $aweberConfig, MessengerInterface $messenger, AweberServiceInterface $aweberService) {
    $this->fields = $fields;
    $this->messenger = $messenger;
    $this->aweberService = $aweberService;
    $this->redirectParams['enable_redirect'] = $aweberConfig->get('enable_redirect');
    $this->redirectParams['redirect_link'] = $aweberConfig->get('redirect_link');
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aweber_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $parameter = NULL) {

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#required' => TRUE,
    ];
    $form['email_lists'] = [
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#title' => t('Email Lists:'),
      '#options' => $this->fields,
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
