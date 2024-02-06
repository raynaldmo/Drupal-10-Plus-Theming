<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'youtube' platform.
 *
 * @Platform(
 *   id = "youtube",
 *   name = @Translation("Youtube"),
 *   urlPrefix = "https://www.youtube.com/",
 * )
 */
class Youtube extends PlatformBase {}
