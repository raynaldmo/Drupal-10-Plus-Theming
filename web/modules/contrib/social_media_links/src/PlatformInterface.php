<?php

namespace Drupal\social_media_links;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines an interface for social media links platform plugins.
 */
interface PlatformInterface extends PluginInspectionInterface {

  /**
   * Get the value for the platform.
   *
   * @return string
   *   The value.
   */
  public function getValue();

  /**
   * Set the value for the platform.
   *
   * @param string $value
   *   The value.
   */
  public function setValue($value);

  /**
   * Return the name of the icon.
   *
   * In most cases, the icon name is the id of the platform.
   *
   * @return string
   *   The name of the icon for the platform.
   */
  public function getIconName();

  /**
   * Return the name of the platform.
   *
   * @return string
   *   The name of the platform.
   */
  public function getName();

  /**
   * Returns the description for the value field.
   *
   * @return string
   *   The description of the value field.
   */
  public function getFieldDescription();

  /**
   * Return the url prefix of the platform.
   *
   * @return string
   *   The url prefix.
   */
  public function getUrlPrefix();

  /**
   * Return the url suffix of the platform.
   *
   * @return string
   *   The url suffix.
   */
  public function getUrlSuffix();

  /**
   * Get the full url for the platform.
   *
   * Return the full url, including urlPrefix, user value and urlSuffix
   * This method is useful to change the url to match platform specific
   * requirements.
   * E.g.: "mailto:VALUE" for email platform or "user-path:/" for internal urls.
   *
   * @return \Drupal\Core\Url
   *   Returns the full Url object for the platform.
   */
  public function getUrl();

  /**
   * Generates the final url for the output.
   *
   * @param \Drupal\Core\Url $url
   *   A Url object with the full plattform url.
   *
   * @return string
   *   The url to the platform (with the user value).
   */
  public function generateUrl(Url $url);

  /**
   * Return value for the title and WAI-ARIA attribute.
   *
   * @return string
   *   The description.
   */
  public function getDescription();

  /**
   * Set the description.
   *
   * @param string $description
   *   The description.
   */
  public function setDescription($description);

  /**
   * Validates the user input of a platform before the value is saved.
   *
   * @return mixed
   *   The result of the validation.
   */
  public static function validateValue(array &$element, FormStateInterface $form_state, array $form);

}
