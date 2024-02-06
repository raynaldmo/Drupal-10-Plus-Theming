<?php

namespace Drupal\social_media_links_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_media_links\IconsetBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Plugin implementation of the 'social_media_links_field_default' formatter.
 *
 * @FieldFormatter(
 *   id = "social_media_links_field_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "social_media_links_field",
 *   }
 * )
 */
class SocialMediaLinksFieldDefaultFormatter extends FormatterBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $platforms = $this->getPlatformsWithValues($items);
    if (count($platforms) < 1) {
      return [];
    }

    $iconset_style = IconsetBase::explodeStyle($items->getSetting('iconset'));
    $iconset = $this->getIconset($iconset_style['iconset']);

    $link_attributes = $this->getSetting('link_attributes');

    foreach ($link_attributes as $key => $value) {
      if ($value === '<none>') {
        unset($link_attributes[$key]);
      }
    }

    foreach ($platforms as $platform_id => $platform) {
      $platforms[$platform_id]['element'] = (array) $iconset['instance']->getIconElement($platform['instance'], $iconset_style['style']);
      $platforms[$platform_id]['attributes'] = new Attribute($link_attributes);

      if (!empty($platform['instance']->getDescription())) {
        $platforms[$platform_id]['attributes']->setAttribute('aria-label', $this->t($platform['instance']->getDescription()));
        $platforms[$platform_id]['attributes']->setAttribute('title', $this->t($platform['instance']->getDescription()));
      }
    }

    $output = [
      '#theme' => 'social_media_links_platforms',
      '#platforms' => $platforms,
      '#appearance' => $this->getSetting('appearance'),
      '#attached' => [
        'library' => ['social_media_links/social_media_links.theme'],
      ],
    ];

    if ($iconset['instance']->getPath() === 'library' && (array) $library = $iconset['instance']->getLibrary()) {
      $output['#attached']['library'] = array_merge_recursive($output['#attached']['library'], $library);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'appearance' => [],
      'link_attributes' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $config = $this->getSettings();

    $element['appearance'] = [
      '#type' => 'details',
      '#title' => $this->t('Appearance'),
      '#tree' => TRUE,
    ];
    $element['appearance']['orientation'] = [
      '#type' => 'select',
      '#title' => $this->t('Orientation'),
      '#options' => [
        'v' => $this->t('vertical'),
        'h' => $this->t('horizontal'),
      ],
      '#default_value' => isset($config['appearance']['orientation']) ? $config['appearance']['orientation'] : 'h',
    ];
    $element['appearance']['show_name'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show name'),
      '#description' => $this->t('Show the platform name next to the icon.'),
      '#default_value' => isset($config['appearance']['show_name']) ? $config['appearance']['show_name'] : 0,
    ];

    // Link Attributes.
    $element['link_attributes'] = [
      '#type' => 'details',
      '#title' => $this->t('Link attributes'),
      '#tree' => TRUE,
    ];
    $element['link_attributes']['target'] = [
      '#type' => 'select',
      '#title' => $this->t('Default target'),
      '#default_value' => isset($config['link_attributes']['target']) ? $config['link_attributes']['target'] : '<none>',
      '#options' => [
        '<none>' => $this->t('Remove target attribute'),
        '_blank' => $this->t('Open in a new browser window or tab (_blank)'),
        '_self' => $this->t('Open in the current window (_self)'),
        '_parent' => $this->t('Open in the frame that is superior to the frame the link is in (_parent)'),
        '_top' => $this->t('Cancel all frames and open in full browser window (_top)'),
      ],
    ];
    $element['link_attributes']['rel'] = [
      '#type' => 'select',
      '#title' => $this->t('Default rel'),
      '#default_value' => isset($config['link_attributes']['rel']) ? $config['link_attributes']['rel'] : '<none>',
      '#options' => [
        '<none>' => $this->t('Remove rel attribute'),
        'nofollow' => $this->t('Set nofollow'),
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $config = $this->getSettings();
    $summary = [];

    if (empty($config['appearance']['orientation'])) {
      $config['appearance']['orientation'] = 'h';
    }
    if (empty($config['appearance']['show_name'])) {
      $config['appearance']['show_name'] = 0;
    }

    $orientation = $config['appearance']['orientation'] == 'v' ? $this->t('vertical') : $this->t('horizontal');
    $summary[] = $this->t('Orientation: @orientation', ['@orientation' => $orientation]);

    $show_name = (isset($config['appearance']['show_name']) && $config['appearance']['show_name']) ? $this->t('Yes') : $this->t('No');
    $summary[] = $this->t('Show name: @show_name', ['@show_name' => $show_name]);

    return $summary;
  }

  /**
   * Get the platforms that have values.
   *
   * @return array
   *   $platforms.
   */
  protected function getPlatformsWithValues(FieldItemListInterface $items) {
    $platform_settings = $items->getSetting('platforms');

    $all_platforms_available = TRUE;
    foreach ($platform_settings as $platform_id => $platform) {
      if ($platform['enabled']) {
        $all_platforms_available = FALSE;
        break;
      }
    }

    $platforms = [];
    foreach ($items as $item) {
      // We have two possible structures where the platform values can be
      // stored.
      // * If the select widget was used the values are saved in two fields
      // (platform and value).
      // * If the default list widget was used the values are saved in a
      // multidimensional array structure (platform_values).
      if (empty($item->platform_values)) {
        // Select widget fields handling.
        if ($all_platforms_available || isset($platform_settings[$item->platform]['enabled']) && $platform_settings[$item->platform]['enabled']) {
          $platforms[$item->platform] = [
            'value' => $item->value,
            'weight' => $platform_settings[$item->platform]['weight'],
            'description' => $platform_settings[$item->platform]['description'],
          ];
        }
      }
      else {
        // Default list field handling.
        $platform_values = $item->platform_values;

        foreach ($platform_values as $platform_id => $platform_value) {
          if ($all_platforms_available || !empty($platform_value['value']) && isset($platform_settings[$platform_id]['enabled']) && $platform_settings[$platform_id]['enabled']) {
            $platforms[$platform_id] = [
              'value' => $platform_value['value'],
              'weight' => $platform_settings[$platform_id]['weight'],
              'description' => $platform_settings[$platform_id]['description'],
            ];
          }
        }
      }
    }

    return \Drupal::service('plugin.manager.social_media_links.platform')->getPlatformsWithValue($platforms);
  }

  /**
   * Get the iconset.
   *
   * @param string $iconset
   *   The iconset id.
   *
   * @return array
   *   $iconsets
   */
  protected function getIconset($iconset) {
    $iconsets = \Drupal::service('plugin.manager.social_media_links.iconset')->getIconsets();
    return $iconsets[$iconset];
  }

}
