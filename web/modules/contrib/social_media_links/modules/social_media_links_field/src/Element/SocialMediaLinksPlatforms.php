<?php

namespace Drupal\social_media_links_field\Element;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides an social media links form element.
 *
 * @FormElement("social_media_links_platforms")
 */
class SocialMediaLinksPlatforms extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    return [
      '#available_platforms' => [],
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#process' => [
        [$class, 'processElement'],
      ],
      '#theme_wrappers' => ['container'],
    ];
  }

  /**
   * Process the platform form element.
   *
   * @param array $element
   *   The form element to process.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processElement(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $id_prefix = implode('-', $element['#parents']);
    $wrapper_id = Html::getUniqueId($id_prefix . '-ajax-wrapper');
    $value = $element['#value'];

    // If only one available platform selectable, remove the platform
    // selection and display the value form for the platform directly.
    if (count($element['#available_platforms']) < 2) {
      $value['platform'] = array_keys($element['#available_platforms'])[0];
      $element = static::platformElements($element, $value);
      $element['value']['#title_display'] = 'before';
      return $element;
    }

    $element = [
      '#tree' => TRUE,
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
      '#wrapper_id' => $wrapper_id,
    ] + $element;

    $element['platform'] = [
      '#type' => 'select',
      '#title' => t('Platform'),
      '#options' => $element['#available_platforms'],
      '#default_value' => $value['platform'],
      '#empty_value' => '',
      '#ajax' => [
        'callback' => [get_called_class(), 'ajaxRefresh'],
        'wrapper' => $wrapper_id,
      ],
      '#weight' => -100,
    ];

    if (!empty($value['platform'])) {
      $element = static::platformElements($element, $value);
    }

    return $element;
  }

  /**
   * Builds the specific platform elements.
   *
   * @param array $element
   *   The existing form elment array.
   * @param array $value
   *   The platform values.
   *
   * @return array
   *   The modified form element array containing the platform specific
   *   elements.
   */
  protected static function platformElements(array $element, array $value) {
    $platforms = \Drupal::service('plugin.manager.social_media_links.platform')->getPlatforms();
    $selected_platform = $platforms[$value['platform']];

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $selected_platform['name']->render(),
      '#title_display' => 'invisible',
      '#size' => 40,
      '#default_value' => isset($value['value']) ? $value['value'] : '',
      '#field_prefix' => $selected_platform['instance']->getUrlPrefix(),
      '#field_suffix' => $selected_platform['instance']->getUrlSuffix(),
      '#element_validate' => [
        [
          get_class($selected_platform['instance']),
          'validateValue',
        ],
      ],
    ];
    if (!empty($selected_platform['instance']->getFieldDescription())) {
      $element['value']['#description'] = $selected_platform['instance']->getFieldDescription();
    }

    return $element;
  }

  /**
   * Ajax callback.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   $platform_field_element
   */
  public static function ajaxRefresh(array $form, FormStateInterface $form_state) {
    $platform_element = $form_state->getTriggeringElement();
    $platform_field_element = NestedArray::getValue($form, array_slice($platform_element['#array_parents'], 0, -2));
    unset($platform_field_element['_weight']);

    return $platform_field_element;
  }

}
