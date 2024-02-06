<?php

namespace Drupal\social_media_links;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Manager class for the iconset plugins.
 */
class SocialMediaLinksIconsetManager extends DefaultPluginManager {

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
    parent::__construct('Plugin/SocialMediaLinks/Iconset', $namespaces, $module_handler, 'Drupal\social_media_links\IconsetInterface', 'Drupal\social_media_links\Annotation\Iconset');

    $this->alterInfo('social_media_links_iconset_info');
    $this->setCacheBackend($cache_backend, 'social_media_links_iconsets');
  }

  /**
   * Get all iconset plugins.
   *
   * @return array
   *   The iconset plugins.
   */
  public function getIconsets() {
    $plugins = $this->getDefinitions();

    foreach ($plugins as $plugin_id => $plugin) {
      $instance = $this->createInstance($plugin_id);

      if ($instance instanceof IconsetInterface) {
        // Attach the class instance to the plugin definitions.
        $plugins[$plugin_id]['instance'] = $instance;
      }
      else {
        $plugins[$plugin_id] = [];
      }
    }

    return $plugins;
  }

  /**
   * Return a array with the available styles for each (available) iconset.
   *
   * @return array
   *   The array with the styles.
   */
  public function getStyles() {
    $options = [];
    foreach ($this->getIconsets() as $iconset_id => $iconset) {
      // Only display options for iconsets that are installed and available.
      if ($iconset['instance']->getPath()) {
        $styles = $iconset['instance']->getStyle();

        foreach ($styles as $key => $style) {
          $options[$iconset_id][$iconset_id . ':' . $key] = $style;
        }
      }
      else {
        $options[$iconset_id] = [];
      }
    }

    return $options;
  }

}
