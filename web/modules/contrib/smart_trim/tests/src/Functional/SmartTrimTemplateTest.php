<?php

namespace Drupal\Tests\smart_trim\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Class to test templates are correctly applied.
 *
 * @group smart_trim
 */
class SmartTrimTemplateTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'test_page_test',
    'field',
    'filter',
    'text',
    'token',
    'token_filter',
    'smart_trim',
    'filter_test',
    'field_ui',
  ];

  /**
   * A user with admin permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'smart_trim_test_theme';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article', 'name' => 'Article']);

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);

    // Create a long text field that will use template with wrapper.
    FieldStorageConfig::create([
      'field_name' => 'field_wrapped',
      'type' => 'text_long',
      'entity_type' => 'node',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_wrapped',
      'entity_type' => 'node',
      'bundle' => 'article',
      'label' => 'Wrapped field',
    ])->save();

    // Create long text field that will use unwrapped template.
    FieldStorageConfig::create([
      'field_name' => 'field_unwrapped',
      'type' => 'text_long',
      'entity_type' => 'node',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_unwrapped',
      'entity_type' => 'node',
      'bundle' => 'article',
      'label' => 'Unwrapped field',
    ])->save();

    $this->drupalCreateNode([
      'title' => $this->randomString(),
      'id' => 1,
      'type' => 'article',
      'body' => [
        'value' => 'Test [node:content-type]',
        'format' => 'filter_test',
      ],
      'field_wrapped' => [
        'value' => 'Test wrapped field',
        'format' => 'filter_test',
      ],
      'field_unwrapped' => [
        'value' => 'Test unwrapped field',
        'format' => 'filter_test',
      ],
    ])->save();

  }

  /**
   * Test that theme template is being used.
   */
  public function testSmartTrimThemeTemplate(): void {
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 5,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->setComponent('field_wrapped', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 15,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->setComponent('field_unwrapped', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 15,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->save();

    $this->drupalGet('/node/1');

    // Find div following "Body" label.
    $query = $this->xpath('//div[text() = "Body"]/following-sibling::div');
    $this->assertEquals('Test', $query[0]->getText());

    // Find wrapper div.
    $query = $this->xpath('//div[contains(@class, "test-theme-wrapper")]');
    $this->assertEquals('Test wrapped', $query[0]->getText());

    // Find div following "Unwrapped field" label.
    $query = $this->xpath('//div[text() = "Unwrapped field"]/following-sibling::div');
    $this->assertEquals('Test unwrapped Below unwrapped', $query[0]->getText());
  }

  /**
   * Test that theme template allows control of more link wrapper.
   */
  public function testSmartTrimMoreLinkThemeTemplate(): void {
    $display_repository = \Drupal::service('entity_display.repository');
    $more = [
      'display_link' => TRUE,
      'class' => 'more-link',
      'link_trim_only' => FALSE,
      'target_blank' => FALSE,
      'text' => 'More',
      'aria_label' => 'Read more about [node:title]',
    ];
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 5,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => $more,
        ],
      ])
      ->setComponent('field_wrapped', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 15,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => $more,
        ],
      ])
      ->setComponent('field_unwrapped', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 15,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => $more,
        ],
      ])
      ->save();

    $this->drupalGet('/node/1');

    // Find div following "Body" label.
    $query = $this->xpath('//p[text() = "Test"]/following-sibling::div');
    $this->assertEquals('More', $query[0]->getText());
    $this->assertEquals('more-link', $query[0]->getAttribute('class'));

    // Find div following wrapped field div.
    $query = $this->xpath('//div[contains(@class, "test-theme-wrapper")]/following-sibling::div');
    $this->assertEquals('More', $query[0]->getText());
    $this->assertEquals('test-theme-more-wrapper', $query[0]->getAttribute('class'));

    // Find a link following "Below unwrapped" paragraph from template.
    $query = $this->xpath('//p[text() = "Below unwrapped"]/following-sibling::a');
    $this->assertEquals('More', $query[0]->getText());
    // Link has no wrapper, but should still have class.
    $this->assertEquals('more-link', $query[0]->getAttribute('class'));
  }

}
