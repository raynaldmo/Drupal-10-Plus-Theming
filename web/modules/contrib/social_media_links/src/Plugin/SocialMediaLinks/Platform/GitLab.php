<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

use Drupal\social_media_links\PlatformBase;

/**
 * Provides 'gitlab' platform.
 *
 * @Platform(
 *   id = "gitlab",
 *   name = @Translation("GitLab"),
 *   urlPrefix = "https://gitlab.com/",
 * )
 */
class GitLab extends PlatformBase {}
