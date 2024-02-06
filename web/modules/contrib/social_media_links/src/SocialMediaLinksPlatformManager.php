<?php

namespace Drupal\social_media_links;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Manager class for the platform plugins.
 */
class SocialMediaLinksPlatformManager extends DefaultPluginManager {

  /**
   * Constructs an SocialMediaLinksPlatformManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/SocialMediaLinks/Platform', $namespaces, $module_handler, 'Drupal\social_media_links\PlatformInterface', 'Drupal\social_media_links\Annotation\Platform');

    $this->alterInfo('social_media_links_platforms_info');
    $this->setCacheBackend($cache_backend, 'social_media_links_platforms');
  }

  /**
   * Get all platform plugins.
   *
   * @return array
   *   The platform plugins.
   */
  public function getPlatforms() {
    $plugins = $this->getDefinitions();

    // Attach the instance object to the plugin definitions.
    foreach ($plugins as $plugin_id => $plugin) {
      $instance = $this->createInstance($plugin_id);

      if ($instance instanceof PlatformInterface) {
        $plugins[$plugin_id]['instance'] = $instance;
      }
    }

    return $plugins;
  }

  /**
   * Get all platform plugins and sort it by weight from.
   *
   * Platform settings (e.g. block configuration, field settings).
   *
   * @param array $settings
   *   The configuration with the 'weight'.
   *
   * @return array
   *   The platform plugins sorted by weight setting.
   */
  public function getPlatformsSortedByWeight(array $settings) {
    $default_weight = -10;

    $platforms = $this->getPlatforms();
    foreach ($platforms as $platform_id => $platform) {
      $platforms[$platform_id]['weight'] = isset($settings['platforms'][$platform_id]['weight']) ? $settings['platforms'][$platform_id]['weight'] : $default_weight++;
    }

    uasort($platforms, [
      'Drupal\Component\Utility\SortArray',
      'sortByWeightElement',
    ]);
    return $platforms;
  }

  /**
   * The the platform plugins that have values.
   *
   * @return array
   *   The platform plugins that have values.
   */
  public function getPlatformsWithValue(array $platforms, $sort = TRUE) {
    $usedPlatforms = [];

    foreach ($this->getPlatforms() as $platform_id => $platform) {
      if (!empty($platforms[$platform_id]['value'])) {
        $platform['instance']->setValue($platforms[$platform_id]['value']);
        $platform['instance']->setDescription($platforms[$platform_id]['description'] ?? '');

        $usedPlatforms[$platform_id] = $platform;

        $usedPlatforms[$platform_id]['weight'] = $platforms[$platform_id]['weight'];
        $usedPlatforms[$platform_id]['url'] = $platform['instance']->generateUrl($platform['instance']->getUrl());
      }
    }

    if ($sort) {
      uasort($usedPlatforms, [
        'Drupal\Component\Utility\SortArray',
        'sortByWeightElement',
      ]);
    }

    return $usedPlatforms;
  }

}
