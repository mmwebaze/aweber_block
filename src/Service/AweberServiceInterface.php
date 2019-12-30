<?php


namespace Drupal\aweber_block\Service;


interface AweberServiceInterface {
  /**
   * Gets Aweber Customer account authorized for this application.
   *
   * @return mixed
   *   The collection of accounts.
   */
  public function accounts();

  /**
   * Gets subscriber lists associated with an account.
   *
   *
   * @param $accountID
   *   The account ID.
   *
   * @return array|null
   *   The email lists.
   */
  public function lists(int $accountID);

  /**
   * Adds a subscriber to a list.
   *
   * @param int $listID
   *   The List ID.
   *
   * @param array $params
   *   A value key array of Subscriber details such as email, name
   *
   * @return mixed
   */
  public function addSubscribers(int $listID, array $params);

  /**
   * Checks if subscriber exists in a list.
   *
   * @param $email
   *   The subscriber's email address.
   *
   * @param int $listId
   *   The list id.
   *
   * @return mixed
   */
  public function checkSubscriberExistsByEmail($email, int $listId);
}
