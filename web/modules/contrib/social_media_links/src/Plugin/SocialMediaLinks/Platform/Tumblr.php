<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\Component\Utility\UrlHelper;
use Drupal\social_media_links\PlatformBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides 'tumblr' platform.
 *
 * @Platform(
 *   id = "tumblr",
 *   name = @Translation("Tumblr"),
 *   urlPrefix = "",
 * )
 */
class Tumblr extends PlatformBase {

  /**
   * {@inheritdoc}
   */
  public static function validateValue(array &$element, FormStateInterface $form_state, array $form) {
    if (!empty($element['#value'])) {
      if (!UrlHelper::isValid($element['#value'], TRUE)) {
        $form_state->setError($element, t('The entered Tumblr URI is not valid.'));
      }
    }
  }

}
