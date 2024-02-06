<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;
use Drupal\Core\Url;

/**
 * Provides 'contact' platform.
 *
 * @Platform(
 *   id = "contact",
 *   name = @Translation("Contact"),
 *   iconName = "email",
 * )
 */
class Contact extends PlatformBase {

  /**
   * {@inheritdoc}
   */
  public function getUrlPrefix() {
    // Get the url of the site as prefix for the url.
    $url = Url::fromUserInput('/', ['absolute' => TRUE]);
    return $url->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    // Generate the internal url based on the user input.
    // See Url::fromUserInput() for more information.
    return Url::fromUserInput('/' . $this->getValue() . $this->getUrlSuffix());
  }

}
