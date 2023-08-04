<?php

namespace Drupal\smart_trim\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\smart_trim\TruncateHTML;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Utility\Token;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Plugin implementation of the 'smart_trim' formatter.
 *
 * @FieldFormatter(
 *   id = "smart_trim",
 *   label = @Translation("Smart trimmed"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "string",
 *     "string_long"
 *   }
 * )
 */
class SmartTrimFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The truncate HTML service.
   *
   * @var \Drupal\smart_trim\TruncateHTML
   */
  protected TruncateHTML $truncateHtml;

  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected Token $token;

  /**
   * Module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\smart_trim\TruncateHTML $truncate_html
   *   The truncate HTML service.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, TruncateHTML $truncate_html, Token $token, ModuleHandlerInterface $module_handler) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->truncateHtml = $truncate_html;
    $this->token = $token;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('smart_trim.truncate_html'),
      $container->get('token'),
      $container->get('module_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'trim_length' => '600',
      'trim_type' => 'chars',
      'trim_suffix' => '',
      'wrap_output' => FALSE,
      'wrap_class' => 'trimmed',
      'more' => [
        'display_link' => FALSE,
        'class' => 'more-link',
        'link_trim_only' => FALSE,
        'target_blank' => FALSE,
        'text' => 'More',
        'aria_label' => 'Read more about [node:title]',
      ],
      'summary_handler' => 'full',
      'trim_options' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $field_name = $this->fieldDefinition->getFieldStorageDefinition()->getName();

    $element['trim_length'] = [
      '#title' => $this->t('Trim length'),
      '#type' => 'textfield',
      '#size' => 10,
      '#default_value' => $this->getSetting('trim_length'),
      '#min' => 0,
      '#required' => TRUE,
    ];

    $element['trim_type'] = [
      '#title' => $this->t('Trim units'),
      '#type' => 'select',
      '#options' => [
        'chars' => $this->t("Characters"),
        'words' => $this->t("Words"),
      ],
      '#default_value' => $this->getSetting('trim_type'),
    ];

    $element['trim_suffix'] = [
      '#title' => $this->t('Suffix'),
      '#type' => 'textfield',
      '#size' => 10,
      '#default_value' => $this->getSetting('trim_suffix'),
    ];

    if ($this->fieldDefinition->getType() == 'text_with_summary') {
      $element['summary_handler'] = [
        '#title' => $this->t('Summary'),
        '#type' => 'select',
        '#options' => [
          'full' => $this->t("Use summary if present, and do not trim"),
          'trim' => $this->t("Use summary if present, honor trim settings"),
          'ignore' => $this->t("Do not use summary"),
        ],
        '#default_value' => $this->getSetting('summary_handler'),
      ];
    }

    $trim_options_value = $this->getSetting('trim_options');
    $element['trim_options'] = [
      '#title' => $this->t('Additional options'),
      '#type' => 'checkboxes',
      '#options' => [
        'text' => $this->t('Strip HTML'),
        'trim_zero' => $this->t('Honor a zero trim length'),
        'replace_tokens' => $this->t('Replace tokens before trimming'),
      ],
      '#default_value' => empty($trim_options_value) ? [] : array_keys(array_filter($trim_options_value)),
    ];

    $element['wrap_output'] = [
      '#title' => $this->t('Wrap trimmed content?'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('wrap_output'),
      '#description' => $this->t('Adds a wrapper div to trimmed content. This option is deprecated and will be removed in Smart Trim 3.0.0. Please override the smart-trim.html.twig template file to customize output.'),
    ];

    $element['wrap_class'] = [
      '#title' => $this->t('Wrapped content class.'),
      '#type' => 'textfield',
      '#size' => 20,
      '#default_value' => $this->getSetting('wrap_class'),
      '#description' => $this->t('If wrapping, define the class name here.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $field_name . '][settings_edit_form][settings][wrap_output]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $element['more'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('<em>More</em> link'),
      '#weight' => 10,
    ];

    $default_settings = self::defaultSettings();
    $more_settings = $this->getSetting('more');
    $more_states = [
      'visible' => [
        ':input[name="fields[body][settings_edit_form][settings][more][display_link]"]' => ['checked' => TRUE],
      ],
    ];
    $more_required_states = $more_states + [
      'required' => [
        ':input[name="fields[body][settings_edit_form][settings][more][display_link]"]' => ['checked' => TRUE],
      ],
    ];

    $element['more']['display_link'] = [
      '#title' => $this->t('Display <em>More</em> link?'),
      '#type' => 'checkbox',
      '#default_value' => $more_settings['display_link'] ?? $default_settings['more']['display_link'],
      '#description' => $this->t('Displays a link to the entity (if one exists)'),
    ];

    $element['more']['link_trim_only'] = [
      '#title' => $this->t('Display <em>More</em> link only when content is trimmed?'),
      '#type' => 'checkbox',
      '#default_value' => $more_settings['link_trim_only'] ?? $default_settings['more']['link_trim_only'],
      '#description' => $this->t('Only display <em>More</em> link if content is actually trimmed.'),
      '#states' => $more_states,
    ];

    $element['more']['target_blank'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Open <em>More</em> link in new window'),
      '#return_value' => '_blank',
      '#default_value' => $more_settings['target_blank'] ?? $default_settings['more']['target_blank'],
      '#states' => $more_states,
    ];

    $element['more']['text'] = [
      '#title' => $this->t('<em>More</em> link text'),
      '#type' => 'textfield',
      '#size' => 20,
      '#default_value' => $more_settings['text'] ?? $default_settings['more']['text'],
      '#description' => $this->t('If displaying <em>More</em> link, enter the text for the link. This field supports tokens.'),
      '#states' => $more_required_states,
    ];

    $element['more']['aria_label'] = [
      '#title' => $this->t('<em>More</em> link aria-label'),
      '#type' => 'textfield',
      '#size' => 30,
      '#default_value' => $more_settings['aria_label'] ?? $default_settings['more']['aria_label'],
      '#description' => $this->t('If displaying <em>More</em> link, provide additional context for screen-reader users. In most cases, the aria-label value will be announced instead of the link text. This field supports tokens.'),
      '#states' => $more_states,
    ];

    $element['more']['class'] = [
      '#title' => $this->t('<em>More</em> link class'),
      '#type' => 'textfield',
      '#size' => 20,
      '#default_value' => $more_settings['class'] ?? $default_settings['more']['class'],
      '#description' => $this->t('If displaying <em>More</em> link, add a custom class for formatting.'),
      '#states' => $more_states,
    ];

    $element['more']['token_browser'] = [
      '#type' => 'item',
      '#theme' => 'token_tree_link',
      '#token_types' => [$this->fieldDefinition->getTargetEntityTypeId()],
      '#states' => $more_states,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $type = $this->t('words');
    if ($this->getSetting('trim_type') == 'chars') {
      $type = $this->t('characters');
    }
    $trim_string = $this->getSetting('trim_length') . ' ' . $type;
    $summary[] = $trim_string;

    if (mb_strlen((trim($this->getSetting('trim_suffix'))))) {
      $trim_string = $this->t("Suffix: %suffix", ['%suffix' => trim($this->getSetting('trim_suffix'))]);
      $summary[] = $trim_string;
    }

    // Summary message line regarding "more" link.
    $more_settings = $this->getSetting('more');
    if ($more_settings['display_link'] ?? FALSE) {
      // Add more text to summary.
      $summary[] = $this->t(
        "<em>More</em> link enabled, text: %text",
        ['%text' => $more_settings['text'] ?? '']
      );

      if ($more_settings['link_trim_only'] ?? FALSE) {
        $summary[] = $this->t("Only display <em>More</em> link when trimmed");
      }

      if ($more_settings['target_blank'] ?? FALSE) {
        $summary[] = $this->t("Open <em>More</em> link in new window");
      }

      $summary[] = $this->t(
        "<em>More</em> link aria-label: %label",
        ['%label' => $more_settings['aria_label'] ?? '']
      );

      $summary[] = $this->t(
        "<em>More</em> link class: %class",
        ['%class' => $more_settings['class'] ?? '']
      );
    }

    if ($this->getSetting('trim_options')) {
      $options = $this->getSetting('trim_options');
      foreach ($options as $key => $option) {
        if ($option) {
          switch ($key) {
            case 'text':
              $trim_string = $this->t('Strip HTML');
              $summary[] = $trim_string;
              break;

            case 'trim_zero':
              $trim_string = $this->t('Honor a zero trim length');
              $summary[] = $trim_string;
              break;

            case 'replace_tokens':
              $trim_string = $this->t('Replace tokens before trimming');
              $summary[] = $trim_string;
              break;

          }
        }
      }
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL): array {
    $element = [];
    $setting_trim_options = $this->getSetting('trim_options');
    $settings_summary_handler = $this->getSetting('summary_handler');
    $entity = $items->getEntity();
    $tokenData = [$entity->getEntityTypeId() => $entity];

    foreach ($items as $delta => $item) {
      if ($settings_summary_handler != 'ignore' && !empty($item->summary)) {
        $output = $item->summary;
      }
      else {
        $output = $item->value;
      }

      // Process additional options (currently only HTML on/off).
      if (!empty($setting_trim_options)) {
        // Allow a zero length trim.
        if (!empty($setting_trim_options['trim_zero']) && $this->getSetting('trim_length') == 0) {
          // If the summary is empty, trim to zero length.
          if (empty($item->summary)) {
            $output = '';
          }
          elseif ($settings_summary_handler != 'full') {
            $output = '';
          }
        }

        // Replace tokens before trimming.
        if (!empty($setting_trim_options['replace_tokens'])) {
          $output = $this->token->replace($output, $tokenData, [
            'langcode' => $langcode,
          ]);
        }

        if (!empty($setting_trim_options['text'])) {
          // Strip caption.
          $output = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', ' ', $output);

          // Strip script.
          $output = preg_replace('/<script[^>]*>.*?<\/script>/is', ' ', $output);

          // Strip style.
          $output = preg_replace('/<style[^>]*>.*?<\/style>/is', ' ', $output);

          // Strip tags.
          // Add space before each tag to ensure words don't run together.
          // Logic via https://stackoverflow.com/questions/12824899/strip-tags-replace-tags-by-space-rather-than-deleting-them
          $output = str_replace('<', ' <', $output);
          $output = strip_tags($output);
          $output = str_replace('  ', ' ', $output);
          $output = trim($output);

          // Strip out line breaks.
          $output = preg_replace('/\n|\r|\t/m', ' ', $output);

          // Strip out non-breaking spaces.
          $output = str_replace('&nbsp;', ' ', $output);
          $output = str_replace("\xc2\xa0", ' ', $output);

          // Strip out extra spaces.
          $output = trim(preg_replace('/\s\s+/', ' ', $output));
        }
      }

      // Store original output for later comparison.
      $original_output = $output;

      // Make the trim, provided we're not showing a full summary.
      if ($this->getSetting('summary_handler') != 'full' || empty($item->summary)) {
        $length = $this->getSetting('trim_length');
        $ellipse = $this->getSetting('trim_suffix');
        if ($this->getSetting('trim_type') == 'words') {
          $output = $this->truncateHtml->truncateWords($output, $length, $ellipse);
        }
        else {
          $output = $this->truncateHtml->truncateChars($output, $length, $ellipse);
        }
      }
      $element[$delta] = [
        '#theme' => 'smart_trim',
        '#output' => [
          '#type' => 'processed_text',
          '#text' => $output,
          '#format' => $item->format,
        ],
        '#wrap_output' => $this->getSetting('wrap_output'),
        '#wrapper_class' => $this->getSetting('wrap_class'),
        '#field' => $item->getParent()->getName(),
        '#entity_type' => $item->getParent()->getEntity()->getEntityTypeId(),
        '#entity_bundle' => $item->getParent()->getEntity()->bundle(),
      ];

      // Add the link, if there is one!
      // The entity must have an id already. Content entities usually get their
      // IDs by saving them. In some cases, eg: Inline Entity Form preview there
      // is no ID until everything is saved.
      // https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!Entity.php/function/Entity%3A%3AtoUrl/8.2.x
      $more_settings = $this->getSetting('more');
      if (($more_settings['display_link'] ?? FALSE) && $entity->id() && $entity->hasLinkTemplate('canonical')) {
        if (
          strpos(strrev($output), strrev('<!--break-->')) !== 0 &&
          (($more_settings['link_trim_only'] ?? FALSE) !== TRUE) ||
          ($original_output != $output)
        ) {
          $more = $more_settings['text'];
          $this->token->replace($more, $tokenData, [
            'langcode' => $langcode,
          ]);
          $class = $more_settings['class'];
          $target = $more_settings['target_blank'];
          $link = $entity->toLink($more);
          $project_link = $link->toRenderable();
          $project_link['#attributes']['class'] = [$class];

          // Allow other modules to modify the read more link before it's
          // created.
          $this->moduleHandler->invokeAll('smart_trim_link_modify', [
            $entity,
            &$more,
            &$link,
          ]);

          // Ensure we don't create an empty aria-label attribute.
          $aria_label = $more_settings['aria_label'];
          if ($aria_label) {
            $project_link['#attributes']['aria-label'] = $this->token->replace($aria_label, $tokenData, [
              'langcode' => $langcode,
            ]);
          }

          if ($target) {
            $project_link['#attributes']['target'] = "_blank";
          }

          $element[$delta]['#more'] = $project_link;
          $element[$delta]['#more_wrapper_class'] = $class;
        }
      }
    }
    return $element;
  }

}
