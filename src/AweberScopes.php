<?php


namespace Drupal\aweber_block;


final class AweberScopes {
  const SCOPES = array( 'account_read' => 'account.read', 'list_read' => 'list.read', 'list_write' => 'list.write',
      'subscriber_read' => 'subscriber.read', 'subscriber_write' => 'subscriber.write', 'email_read' => 'email.read',
      'email_write' => 'email.write', 'subscriber_read-extended' => 'subscriber.read-extended'
    );
}
