<?php

namespace Drupal\aweber_block\Form;

use Drupal\aweber_block\Service\AweberServiceInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;

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

  private $fields = [];

  public function __construct(array $fields, MessengerInterface $messenger, AweberServiceInterface $aweberService) {
    $this->fields = $fields;
    $this->messenger = $messenger;
    $this->aweberService = $aweberService;
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
        }
      }
    }
  }
}
