<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Iconset;

use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetInterface;

/**
 * Provides 'nouveller' iconset.
 *
 * @Iconset(
 *   id = "nouveller",
 *   name = "Social Media Bookmark Icon +",
 *   publisher = "Nouveller",
 *   publisherUrl = "http://www.nouveller.com",
 *   downloadUrl = "http://www.nouveller.com/general/free-social-media-bookmark-icon-pack-the-ever-growing-icon-set",
 * )
 */
class Nouveller extends IconsetBase implements IconsetInterface {

  /**
   * {@inheritdoc}
   */
  public function getStyle() {
    return [
      '16' => '16x16',
      '32' => '32x32',
      'buttons' => '122x42',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIconPath($icon_name, $style) {
    switch ($icon_name) {
      case 'googleplus':
        $icon_name = 'google';
        break;
    }

    return $this->path . '/' . $style . '/' . $icon_name . '.png';
  }

}
