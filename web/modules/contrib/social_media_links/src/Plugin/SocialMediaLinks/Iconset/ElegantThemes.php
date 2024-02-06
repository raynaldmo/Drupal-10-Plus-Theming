<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Iconset;

use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetInterface;

/**
 * Provides 'elegantthemes' iconset.
 *
 * @Iconset(
 *   id = "elegantthemes",
 *   name = "Elegant Themes Icons",
 *   publisher = "Elegant Themes",
 *   publisherUrl = "http://www.elegantthemes.com/",
 *   downloadUrl = "http://www.elegantthemes.com/blog/resources/beautiful-free-social-media-icons",
 * )
 */
class ElegantThemes extends IconsetBase implements IconsetInterface {

  /**
   * {@inheritdoc}
   */
  public function getStyle() {
    return [
      '32' => '32x32',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIconPath($icon_name, $style) {
    return $this->path . '/PNG/' . $icon_name . '.png';
  }

}
