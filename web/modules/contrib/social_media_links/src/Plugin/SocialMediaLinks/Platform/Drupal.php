<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'drupal' platform.
 *
 * @Platform(
 *   id = "drupal",
 *   name = @Translation("Drupal"),
 *   urlPrefix = "https://www.drupal.org/u/",
 * )
 */
class Drupal extends PlatformBase {}
