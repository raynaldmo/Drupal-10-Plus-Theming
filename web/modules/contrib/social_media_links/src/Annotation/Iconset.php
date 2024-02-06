<?php

namespace Drupal\social_media_links\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a iconset item annotation object.
 *
 * Plugin Namespace: Plugin\SocialMediaLinks\Iconset.
 *
 * @see \Drupal\social_media_links\SocialMediaLinksIconsetManager
 * @see plugin_api
 *
 * @Annotation
 */
class Iconset extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the iconset.
   *
   * @var string
   */
  public $name;

  /**
   * The name of the publisher.
   *
   * @var string
   */
  public $publisher;

  /**
   * The url of the website of the publisher.
   *
   * @var string
   */
  public $publisherUrl;

  /**
   * The url to download the iconset.
   *
   * @var string
   */
  public $downloadUrl;

}
