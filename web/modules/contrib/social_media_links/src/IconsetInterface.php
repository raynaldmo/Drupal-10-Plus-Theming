<?php

namespace Drupal\social_media_links;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for social media links iconset plugins.
 */
interface IconsetInterface extends PluginInspectionInterface {

  /**
   * Return the name of the iconset.
   *
   * @return string
   *   The name of the iconset.
   */
  public function getName();

  /**
   * Return the name of the publisher.
   *
   * @return string
   *   Publisher name.
   */
  public function getPublisher();

  /**
   * Return the url of the publisher.
   *
   * @return string
   *   URL to the publisher website.
   */
  public function getPublisherUrl();

  /**
   * Return the url to download the iconset.
   *
   * @return string
   *   URL to download the iconset.
   */
  public function getDownloadUrl();

  /**
   * Return the available styles.
   *
   * @return array
   *   The available styles/sizes of the iconset.
   */
  public function getStyle();

  /**
   * Return the path of an icon for the given platform (iconName) and style.
   *
   * @param string $icon_name
   *   The name of the icon/platform.
   * @param string $style
   *   The style/size.
   *
   * @return string
   *   The path to the icon of a platform.
   */
  public function getIconPath($icon_name, $style);

}
