<?php

namespace Drupal\aweber_block\Plugin\Block;

use Drupal\aweber_block\Form\AweberForm;
use Drupal\aweber_block\Service\AweberServiceInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'AweberBlock' block.
 *
 * @Block(
 *  id = "aweber_block",
 *  admin_label = @Translation("Aweber block"),
 * )
 */
class AweberBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The Aweber manager service.
   *
   * @var AweberServiceInterface
   */
  protected $aweberService;
  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilderService;
  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  //protected $messenger;
  /**
   * The Email list options.
   *
   * @var array|null
   */
  protected $listOptions = array();
  /**
   * The selected Email lists.
   *
   * @var array
   */
  protected $lists = array();
  /**
   * The Aweber Accounts.
   *
   * @var array
   */
  protected $accounts;
  protected $accoundId;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, AweberServiceInterface $aweberService,
                              FormBuilder $formBuilderService, ConfigFactoryInterface $configFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    //$this->configFactory =
    $this->aweberService = $aweberService;
    $this->formBuilderService = $formBuilderService;
    //$this->messenger = $messenger;
    $this->accounts = $this->aweberService->accounts();

    if (!empty($this->accounts)){
      $this->accoundId = $this->accounts[0]['id'];
      $aweberConfig = $configFactory->getEditable('aweber_block.aweberblockconfig');
      $this->listOptions = $this->aweberService->lists($this->accounts[0]['id']);
      $aweberConfig->set('aweber_account_id', $this->accounts[0]['id']);
      $aweberConfig->save();
    }
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('aweber_block.manager'),
      $container->get('form_builder'),
      $container->get('config.factory')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $emailLists = $config['aweber_block_email_selected_lists'];

    $form['email_lists'] = [
      '#title' => $this->t('Email lists'),
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#description' => $this->t('Aweber email lists available.'),
      '#options' => $this->listOptions,
      '#default_value' => $emailLists,
      '#required' => TRUE,
    ];

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $emailLists = $config['aweber_block_email_lists'];
    $aweberForm = new AweberForm($emailLists, $this->messenger(), $this->aweberService);
    $form = $this->formBuilderService->getForm($aweberForm);

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $selectedLists = $form_state->getValue('email_lists');

    foreach ($selectedLists as $selectedList => $value) {
      if ($value != 0) {
        $this->lists[$value] = $this->listOptions[$value];
      }
    }

    $this->setConfigurationValue('aweber_block_email_selected_lists', $selectedLists);
    $this->setConfigurationValue('aweber_block_email_lists', $this->lists);
  }
}
