<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'github' platform.
 *
 * @Platform(
 *   id = "github",
 *   name = @Translation("GitHub"),
 *   urlPrefix = "https://github.com/",
 * )
 */
class GitHub extends PlatformBase {}
