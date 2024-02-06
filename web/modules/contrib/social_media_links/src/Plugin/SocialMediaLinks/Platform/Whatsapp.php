<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'whatsapp' platform.
 *
 * @Platform(
 *   id = "whatsapp",
 *   name = @Translation("Whatsapp"),
 *   urlPrefix = "https://api.whatsapp.com/send?phone=",
 * )
 */
class Whatsapp extends PlatformBase {}
