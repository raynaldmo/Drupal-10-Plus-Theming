<?php

namespace Drupal\social_media_links;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for iconset.
 */
abstract class IconsetBase extends PluginBase implements IconsetInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected $path = '';

  /**
   * {@inheritdoc}
   */
  protected $finder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, IconsetFinderService $finder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->finder = $finder;
    $this->setPath($plugin_id);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('social_media_links.finder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPublisher() {
    return $this->pluginDefinition['publisher'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPublisherUrl() {
    return $this->pluginDefinition['publisherUrl'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDownloadUrl() {
    return $this->pluginDefinition['downloadUrl'];
  }

  /**
   * Get the path to the iconset.
   *
   * @return string
   *   The path.
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * Set the path for the given iconset.
   *
   * @param string $iconset_id
   *   The id of the iconset.
   */
  public function setPath($iconset_id) {
    $this->path = $this->finder->getPath($iconset_id);
  }

  /**
   * Get the library.
   *
   * This method is used if the iconset should be used via the Library API.
   *
   * @return array
   *   The library.
   */
  public function getLibrary() {
    return NULL;
  }

  /**
   * Get the iconset element.
   *
   * @param string $platform
   *   The platform id.
   * @param string $style
   *   The style.
   *
   * @return array
   *   The array with the icon element.
   */
  public function getIconElement($platform, $style) {
    $iconName = $platform->getIconName();
    $path = $this->getIconPath($iconName, $style);

    $icon = [
      '#theme' => 'image',
      '#uri' => $path,
    ];

    return $icon;
  }

  /**
   * Explode the styles.
   *
   * @param string $style
   *   The styles.
   * @param bool $key
   *   If TRUE: Only the given key is returned.
   *
   * @return string|array
   *   The array with the styles or a string if a $key was given.
   */
  public static function explodeStyle($style, $key = FALSE) {
    $exploded = explode(':', $style);

    if ($key) {
      return $exploded[$key];
    }

    return [
      'iconset' => isset($exploded[0]) ? $exploded[0] : '',
      'style' => isset($exploded[1]) ? $exploded[1] : '',
    ];
  }

}
