<?php

namespace Drupal\Tests\fences\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\fences\Traits\StripWhitespaceTrait;

/**
 * The base class for field output tests.
 *
 * @group fences
 */
abstract class FieldOutputTestBase extends KernelTestBase {

  use StripWhitespaceTrait;

  /**
   * The test field name for the "cardinality" = 1 field.
   *
   * @var string
   */
  protected $fieldNameSingle = 'field_test';

  /**
   * The test field name for the "cardinality" = CARDINALITY_UNLIMITED field.
   *
   * @var string
   */
  protected $fieldNameMultiple = 'field_test_multiple';

  /**
   * The entity type ID.
   *
   * @var string
   */
  protected $entityTypeId = 'entity_test';

  /**
   * The test entity used for testing output.
   *
   * @var \Drupal\entity_test\Entity\EntityTest
   */
  protected $entity;

  /**
   * The entity display under test.
   *
   * @var \Drupal\Core\Entity\Entity\EntityViewDisplay
   */
  protected $entityViewDisplay;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'user',
    'system',
    'field',
    'text',
    'filter',
    'entity_test',
    'field_test',
    'fences',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp():void {
    parent::setUp();

    $this->installEntitySchema($this->entityTypeId);
    $this->installEntitySchema('filter_format');
    $this->renderer = \Drupal::service('renderer');

    // Setup a field and an entity display.
    EntityViewDisplay::create([
      'targetEntityType' => 'entity_test',
      'bundle' => 'entity_test',
      'mode' => 'default',
    ])->save();

    // Create the single cardinality field:
    FieldStorageConfig::create([
      'field_name' => $this->fieldNameSingle,
      'entity_type' => $this->entityTypeId,
      'type' => 'text',
    ])->save();
    FieldConfig::create([
      'entity_type' => $this->entityTypeId,
      'field_name' => $this->fieldNameSingle,
      'bundle' => $this->entityTypeId,
      'label' => 'Field Test',
    ])->save();

    // Create the multiple cardinality field:
    FieldStorageConfig::create([
      'field_name' => $this->fieldNameMultiple,
      'entity_type' => $this->entityTypeId,
      'type' => 'text',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
    ])->save();
    FieldConfig::create([
      'entity_type' => $this->entityTypeId,
      'field_name' => $this->fieldNameMultiple,
      'bundle' => $this->entityTypeId,
      'label' => 'Field Test Multiple',
      'translatable' => FALSE,
    ])->save();

    $this->entityViewDisplay = EntityViewDisplay::load('entity_test.entity_test.default');

    // Create a test entity with a test value.
    $this->entity = EntityTest::create();
    $this->entity->set($this->fieldNameSingle, 'lorem ipsum');
    $this->entity->set($this->fieldNameMultiple, [
      'test value 1',
      'test value 2',
      'test value 3',
    ]);
    $this->entity->save();

    // Set the default filter format.
    FilterFormat::create([
      'format' => 'test_format',
      'name' => $this->randomMachineName(),
    ])->save();
    $this->config('filter.settings')
      ->set('fallback_format', 'test_format')
      ->save();
  }

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupNoLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function onlyFieldTagNoLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function onlyFieldTagWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldAndLabelTagWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function classesAndTagsWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldAndFieldItemTagWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function defaultFieldWithLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupNoLabelItemsWrapperOnlyExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldTagItemsWrapperNoLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupWithLabelAndItemsWrapperExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldTagWithLabelAndItemsWrapperExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldAndLabelTagWithLabelAndItemsWrapperExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function defaultFieldDefaultItemsWrapperNoLabelExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldItemsWrapperAndLabelAllClassesSetExpectedSingle();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupNoLabelItemsWrapperOnlyExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldFieldItemAndItemsWrapperTagNoLabelExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldFieldItemItemsWrapperAndLabelTagWithLabelExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupWithLabelAndItemsWrapperExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function fieldAndItemsWrapperTagWithLabelExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupNoLabelItemTagOnlyExpectedMultiple();

  /**
   * Defines the expected output for the various settings in @fieldTestCases().
   */
  abstract protected function noFieldMarkupWithLabelItemTagOnlyExpectedMultiple();

  /**
   * Test cases for the field output test.
   *
   * @return array
   *   A multidimensional array containing the test data.
   *
   *   Every array entry should be keyed by test name and every array entry's
   *   content should contain the parameters described in @testFieldOutput.
   */
  public function fieldTestCases() {
    return [
      [
        $this->noFieldMarkupNoLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->onlyFieldTagNoLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->noFieldMarkupWithLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->onlyFieldTagWithLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->fieldAndLabelTagWithLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'h3',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->classesAndTagsWithLabelExpectedSingle(),
        $this->fieldNameSingle,
      [
        'fences_field_tag' => 'ul',
        'fences_field_classes' => 'item-list',
        'fences_field_items_wrapper_tag' => 'none',
        'fences_field_items_wrapper_classes' => '',
        'fences_field_item_tag' => 'li',
        'fences_field_item_classes' => 'item-list__item',
        'fences_label_tag' => 'li',
        'fences_label_classes' => 'item-list__label',
      ],
        TRUE,
      ],
      [
        $this->fieldAndFieldItemTagWithLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'h2',
          'fences_field_item_classes' => '',
          'fences_label_tag' => '',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->defaultFieldWithLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => '',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => '',
          'fences_field_item_classes' => '',
          'fences_label_tag' => '',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->noFieldMarkupNoLabelItemsWrapperOnlyExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'article',
          'fences_field_items_wrapper_classes' => 'items-wrapper',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->fieldTagItemsWrapperNoLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->noFieldMarkupWithLabelAndItemsWrapperExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => 'items-wrapper',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->fieldTagWithLabelAndItemsWrapperExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->fieldAndLabelTagWithLabelAndItemsWrapperExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'h3',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->defaultFieldDefaultItemsWrapperNoLabelExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => '',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => '',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => '',
          'fences_field_item_classes' => '',
          'fences_label_tag' => '',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->FieldItemsWrapperAndLabelAllClassesSetExpectedSingle(),
        $this->fieldNameSingle,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => 'tag-class',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => 'items-wrapper',
          'fences_field_item_tag' => 'div',
          'fences_field_item_classes' => 'item-wrapper',
          'fences_label_tag' => 'h2',
          'fences_label_classes' => 'label-class',
        ],
        FALSE,
      ],
      [
        $this->noFieldMarkupNoLabelItemsWrapperOnlyExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'article',
          'fences_field_items_wrapper_classes' => 'items-wrapper',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->fieldFieldItemAndItemsWrapperTagNoLabelExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'div',
          'fences_field_item_classes' => 'item-class',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->fieldFieldItemItemsWrapperAndLabelTagWithLabelExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'div',
          'fences_field_item_classes' => 'item-class',
          'fences_label_tag' => 'h2',
          'fences_label_classes' => 'label-class',
        ],
        TRUE,
      ],
      [
        $this->noFieldMarkupWithLabelAndItemsWrapperExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'div',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'ul',
          'fences_field_items_wrapper_classes' => 'items-wrapper',
          'fences_field_item_tag' => 'li',
          'fences_field_item_classes' => 'item-class',
          'fences_label_tag' => 'h2',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->fieldAndItemsWrapperTagWithLabelExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'article',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'div',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'none',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
      [
        $this->noFieldMarkupNoLabelItemTagOnlyExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'div',
          'fences_field_item_classes' => 'item-wrapper',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        FALSE,
      ],
      [
        $this->noFieldMarkupWithLabelItemTagOnlyExpectedMultiple(),
        $this->fieldNameMultiple,
        [
          'fences_field_tag' => 'none',
          'fences_field_classes' => '',
          'fences_field_items_wrapper_tag' => 'none',
          'fences_field_items_wrapper_classes' => '',
          'fences_field_item_tag' => 'div',
          'fences_field_item_classes' => '',
          'fences_label_tag' => 'none',
          'fences_label_classes' => '',
        ],
        TRUE,
      ],
    ];
  }

  /**
   * Test the field output.
   *
   * @param string $expectedOutput
   *   The expected rendered HTML output.
   * @param string $fieldName
   *   The field name of the field to use for the test.
   * @param array $settings
   *   The fences settings.
   * @param bool $labelVisible
   *   Whether the label should be visible.
   *
   * @dataProvider fieldTestCases
   */
  public function testFieldOutput($expectedOutput, $fieldName, $settings, $labelVisible) {
    // The entity display must be updated because the view method on fields
    // doesn't support passing third party settings.
    $this->entityViewDisplay->setComponent($fieldName, [
      'label' => $labelVisible ? 'above' : 'hidden',
      'settings' => [],
      'type' => 'text_default',
      'third_party_settings' => [
        'fences' => $settings,
      ],
    ])->setStatus(TRUE)->save();
    $field_output = $this->entity->{$fieldName}->view('default');
    $rendered_field_output = $this->stripWhitespace($this->renderer->renderRoot($field_output));
    $this->assertEquals($this->stripWhitespace($expectedOutput), $rendered_field_output);
  }

}
