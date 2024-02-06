<?php

namespace Drupal\social_media_links_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'social_media_links_field' field type.
 *
 * @FieldType(
 *   id = "social_media_links_field",
 *   label = @Translation("Social Media Links Field"),
 *   description = @Translation("Handle links to social media profiles."),
 *   default_widget = "social_media_links_field_default",
 *   default_formatter = "social_media_links_field_default"
 * )
 */
class SocialMediaLinksFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'platforms' => [],
      'iconset' => '',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field) {
    return [
      'columns' => [
        'platform' => [
          'type' => 'varchar',
          'default' => '',
          'length' => '128',
        ],
        'value' => [
          'type' => 'varchar',
          'default' => '',
          'length' => '128',
        ],
        'platform_values' => [
          'type' => 'blob',
          'not null' => TRUE,
          'serialize' => TRUE,
        ],
      ],
      'indexes' => [
        'platform' => ['platform'],
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field) {
    $properties['platform'] = DataDefinition::create('string')
      ->setLabel(t('Platform'));

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Value'));

    $properties['platform_values'] = DataDefinition::create('any')
      ->setLabel(t('Platform Values'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    $platform_settings = $this->getSetting('platforms');

    $element['iconset'] = [
      '#type' => 'select',
      '#title' => $this->t('Icon Style'),
      '#default_value' => $this->getSetting('iconset'),
      '#options' => \Drupal::service('plugin.manager.social_media_links.iconset')->getStyles(),
    ];

    $element['platforms'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Platform'),
        $this->t('Description'),
        $this->t('Weight'),
      ],
      '#prefix' => '<h5>' . $this->t('Platforms') . '</h5>',
      '#suffix' => '<div class="description">' . $this->t('The platforms that are allowed to be used in this field. Select none to allow all platforms.') . '</div>',
      '#tabledrag' => [[
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'platform-order-weight',
      ],
      ],
    ];

    // Keep a note of the highest weight.
    $max_weight = 10;
    $platforms = \Drupal::service('plugin.manager.social_media_links.platform')->getPlatformsSortedByWeight(['platforms' => $this->getSetting('platforms')]);
    foreach ($platforms as $platform_id => $platform) {
      $element['platforms'][$platform_id]['#attributes']['class'][] = 'draggable';
      $element['platforms'][$platform_id]['#weight'] = $platform['weight'];
      if ($platform['weight'] > $max_weight) {
        $max_weight = $platform['weight'];
      }

      $element['platforms'][$platform_id]['enabled'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($platform_settings[$platform_id]['enabled']) ? $platform_settings[$platform_id]['enabled'] : FALSE,
        '#title' => $platform['name']->render(),
        '#title_display' => 'after',
      ];

      $element['platforms'][$platform_id]['description'] = [
        '#type' => 'textfield',
        '#title' => $platform['name']->render(),
        '#title_display' => 'invisible',
        '#description' => $this->t('The description is used for the title and WAI-ARIA attribute.'),
        '#size' => 40,
        '#placeholder' => $this->t('Find / Follow us on %platform', ['%platform' => $platform['name']->render()]),
        '#default_value' => isset($platform_settings[$platform_id]['description']) ? $platform_settings[$platform_id]['description'] : '',
      ];

      $element['platforms'][$platform_id]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for type @type', ['@type' => $platform_id]),
        '#title_display' => 'invisible',
        '#default_value' => $platform['weight'],
        '#attributes' => [
          'class' => [
            'platform-order-weight',
            'platform-order-weight-' . $platform_id,
          ],
        ],
        // Delta: We need to use the max weight + number of platforms,
        // because if they get re-ordered it could start the count again from
        // the max weight, when the last item is dragged to be the first item.
        '#delta' => $max_weight + count($platforms),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $platform = $this->platform;
    $value = $this->value;
    $platform_values = $this->platform_values;

    if (!empty($platform_values)) {
      return FALSE;
    }

    return $value === NULL || $value === '' || $platform === NULL || $platform === '';
  }

}
