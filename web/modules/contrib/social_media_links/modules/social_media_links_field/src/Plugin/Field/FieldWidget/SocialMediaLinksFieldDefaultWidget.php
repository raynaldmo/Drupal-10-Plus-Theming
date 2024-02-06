<?php

namespace Drupal\social_media_links_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'social_media_links_field_default' widget.
 *
 * @FieldWidget(
 *   id = "social_media_links_field_default",
 *   label = @Translation("List with all available platforms"),
 *   field_types = {
 *     "social_media_links_field"
 *   }
 * )
 */
class SocialMediaLinksFieldDefaultWidget extends SocialMediaLinksFieldBaseWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];
    $value = $item->toArray();

    $element += [
      '#type' => 'details',
      '#collapsible' => TRUE,
      '#open' => TRUE,
    ];

    foreach ($this->getAvailablePlatforms() as $platform_id => $platform) {
      $element['platform_values'][$platform_id]['value'] = [
        '#type' => 'textfield',
        '#title' => $platform['name']->render(),
        '#size' => 40,
        '#default_value' => isset($value['platform_values'][$platform_id]['value']) ? $value['platform_values'][$platform_id]['value'] : '',
        '#field_prefix' => $platform['instance']->getUrlPrefix(),
        '#field_suffix' => $platform['instance']->getUrlSuffix(),
        '#required' => $element['#required'] ?? FALSE,
        '#element_validate' => [
          [
            get_class($platform['instance']),
            'validateValue',
          ],
        ],
      ];
      if (!empty($platform['instance']->getFieldDescription())) {
        $element['platform_values'][$platform_id]['value']['#description'] = $platform['instance']->getFieldDescription();
      }
    }

    return $element;
  }

}
