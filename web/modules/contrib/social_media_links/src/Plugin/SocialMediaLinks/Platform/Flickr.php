<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'Flickr' platform.
 *
 * @Platform(
 *   id = "flickr",
 *   name = @Translation("Flickr"),
 *   urlPrefix = "https://www.flickr.com/photos/",
 * )
 */
class Flickr extends PlatformBase {}
