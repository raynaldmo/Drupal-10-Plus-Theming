<?php

namespace Drupal\social_media_links\Plugin\SocialMediaLinks\Platform;

/**
 * Provides 'youtube_channel' platform.
 *
 * @Platform(
 *   id = "youtube_channel",
 *   iconName = "youtube",
 *   name = @Translation("Youtube Channel"),
 *   urlPrefix = "https://www.youtube.com/channel/",
 * )
 */
class YoutubeChannel extends Youtube {}
