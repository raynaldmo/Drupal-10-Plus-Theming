<?php

namespace Drupal\social_media_links\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a platform item annotation object.
 *
 * Plugin Namespace: Plugin\SocialMediaLinks\Platform.
 *
 * @see \Drupal\social_media_links\SocialMediaLinksPlatformManager
 * @see plugin_api
 *
 * @Annotation
 */
class Platform extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * Optional: The name of the icon.
   *
   * In most cases, the iconName is equal to the ID of the platform.
   * If no iconName is set, we use the ID.
   *
   * @var string
   */
  public $iconName;

  /**
   * The name of the platform.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * The description for the platform value field.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $fieldDescription;

  /**
   * The url prefix of the platform.
   *
   * @var string
   */
  public $urlPrefix;

  /**
   * The url suffix of the platform.
   *
   * @var string
   */
  public $urlSuffix;

}
