<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Iconset;

use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetInterface;

/**
 * Provides 'elegantthemes' iconset.
 *
 * @Iconset(
 *   id = "fontawesome",
 *   publisher = "Font Awesome",
 *   publisherUrl = "http://fontawesome.github.io/",
 *   downloadUrl = "http://fortawesome.github.io/Font-Awesome/",
 *   name = "Font Awesome",
 * )
 */
class FontAwesome extends IconsetBase implements IconsetInterface {

  /**
   * {@inheritdoc}
   */
  public function setPath($iconset_id) {
    $this->path = $this->finder->getPath($iconset_id) ? $this->finder->getPath($iconset_id) : 'library';
  }

  /**
   * {@inheritdoc}
   */
  public function getStyle() {
    return [
      '2x' => 'fa-2x',
      '3x' => 'fa-3x',
      '4x' => 'fa-4x',
      '5x' => 'fa-5x',
      'in' => 'fa-in',
      'lg' => 'fa-lg',
      'fw' => 'fa-fw',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIconElement($platform, $style) {
    $icon_name = $platform->getIconName();

    switch ($icon_name) {
      case 'vimeo':
        $icon_name = $icon_name . '-square';
        break;

      case 'googleplus':
        $icon_name = 'google-plus';
        break;

      case 'email':
        $icon_name = 'envelope';
        break;

      case 'website':
        $icon_name = 'home';
        break;

      case 'googleplay':
        $icon_name = 'google-play';
        break;

      case 'meetup':
        $icon_name = 'meetup';
        break;

      case 'patreon':
        $icon_name = 'patreon';
        break;
    }

    if ($icon_name == 'envelope' || $icon_name == 'home' || $icon_name == 'rss') {
      $icon = [
        '#type' => 'markup',
        '#markup' => "<span class='fa fa-$icon_name fa-$style'></span>",
      ];
    }
    else {
      $icon = [
        '#type' => 'markup',
        '#markup' => "<span class='fab fa-$icon_name fa-$style'></span>",
      ];
    }

    return $icon;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrary() {
    if (\Drupal::service('module_handler')->moduleExists('fontawesome')) {
      return parent::getLibrary();
    }
    else {
      return [
        'social_media_links/fontawesome.component',
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIconPath($icon_name, $style) {
    return NULL;
  }

}
