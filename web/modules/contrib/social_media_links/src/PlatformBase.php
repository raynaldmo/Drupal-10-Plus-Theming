<?php

namespace Drupal\social_media_links;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

/**
 * Base class for platform.
 */
class PlatformBase extends PluginBase implements PlatformInterface {

  /**
   * {@inheritdoc}
   */
  protected $value;

  /**
   * {@inheritdoc}
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return Html::escape($this->value ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getIconName() {
    return !empty($this->pluginDefinition['iconName']) ? $this->pluginDefinition['iconName'] : $this->pluginDefinition['id'];
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
  public function getFieldDescription() {
    return isset($this->pluginDefinition['fieldDescription']) ? $this->pluginDefinition['fieldDescription'] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlPrefix() {
    return isset($this->pluginDefinition['urlPrefix']) ? $this->pluginDefinition['urlPrefix'] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlSuffix() {
    return isset($this->pluginDefinition['urlSuffix']) ? $this->pluginDefinition['urlSuffix'] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return Url::fromUri($this->getUrlPrefix() . $this->getValue() . $this->getUrlSuffix());
  }

  /**
   * {@inheritdoc}
   */
  public function generateUrl(Url $url) {
    return $url->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return Html::escape($this->description ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateValue(array &$element, FormStateInterface $form_state, array $form) {
    // Do not allow a URL when the plugin already provides a URL prefix.
    if (!empty($element['#value']) && !empty($element['#field_prefix'])) {
      if (UrlHelper::isExternal($element['#value'])) {
        $form_state->setError($element, t("The entered value %value is a URL. You should enter only the relative part, the URL prefix is automatically prepended.", ['%value' => $element['#value']]));
      }
    }
  }

}
