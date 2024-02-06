<?php

namespace Drupal\social_media_links_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Plugin implementation of the 'social_media_links_field_select' widget.
 *
 * @FieldWidget(
 *   id = "social_media_links_field_select",
 *   label = @Translation("One available platform (selectable)"),
 *   field_types = {
 *     "social_media_links_field"
 *   }
 * )
 */
class SocialMediaLinksFieldSelectWidget extends SocialMediaLinksFieldBaseWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];
    $value = $item->getEntity()->isNew() ? $this->getInitialValues() : $item->toArray();

    $element['platform_element'] = [
      '#type' => 'social_media_links_platforms',
      '#required' => $this->fieldDefinition->isRequired(),
      '#default_value' => $value,
      '#available_platforms' => $this->getAvailablePlatformOptions(),
    ];

    return $element;
  }

  /**
   * Gets the initial values for the widget.
   *
   * @return array
   *   The initial values, keyed by property.
   */
  protected function getInitialValues() {
    $initial_values = [
      'platform' => '',
      'value' => '',
    ];

    return $initial_values;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    $error_element = NestedArray::getValue($element['platform_element'], $violation->arrayPropertyPath);
    return is_array($error_element) ? $error_element : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $delta => $value) {
      $new_values[$delta] = $value['platform_element'];
    }
    return $new_values;
  }

}
