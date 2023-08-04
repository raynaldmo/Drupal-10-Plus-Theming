<?php

namespace Drupal\Tests\smart_trim\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Session\AccountInterface;

/**
 * This class provides methods specifically for testing something.
 *
 * @group smart_trim
 */
class SmartTrimFunctionalTest extends BrowserTestBase {

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
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $user;

  /**
   * A user with admin permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article', 'name' => 'Article']);

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->user = $this->drupalCreateUser();
    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);

    $this->drupalCreateNode([
      'title' => $this->randomString(),
      'id' => 1,
      'type' => 'article',
      'body' => [
        'value' => 'Test [node:content-type]',
        'format' => 'filter_test',
      ],
    ])->save();

    $this->drupalCreateNode([
      'title' => $this->randomString(),
      'id' => 2,
      'type' => 'article',
      'body' => [
        'value' => '<h3>The situation</h3><p>is dire</p><p>and there is no hope.</p>',
        'format' => 'filter_test',
      ],
    ])->save();
  }

  /**
   * Tests if installing the module, won't break the site.
   */
  public function testInstallation() :void {
    $session = $this->assertSession();
    $this->drupalGet('<front>');
    // Ensure the status code is success:
    $session->statusCodeEquals(200);
    // Ensure the correct test page is loaded as front page:
    $session->pageTextContains('Test page text.');
  }

  /**
   * Tests if uninstalling the module, won't break the site.
   */
  public function testUninstallation(): void {
    // Go to uninstallation page to uninstall smart_trim.
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/modules/uninstall');
    $session->statusCodeEquals(200);
    $page->checkField('edit-uninstall-smart-trim');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    // Confirm uninstall.
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('The selected modules have been uninstalled.');
    // Retest the frontpage.
    $this->drupalGet('<front>');
    // Ensure the status code is success.
    $session->statusCodeEquals(200);
    // Ensure the correct test page is loaded as front page.
    $session->pageTextContains('Test page text.');
  }

  /**
   * Tests if the token will not get cut off when trimming by characters.
   */
  public function testTokenNotCutOffTrimTypeCharacters(): void {
    $session = $this->assertSession();
    $display_repository = \Drupal::service('entity_display.repository');
    // Editing our "filter_test" format:
    $edit = [
      'edit-filters-token-filter-status' => 1,
      'edit-filters-filter-html-escape-status' => 0,
    ];
    $this->drupalGet('admin/config/content/formats/manage/filter_test');
    $this->submitForm($edit, 'Save configuration');
    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'trim_options' => [
            'replace_tokens' => TRUE,
          ],
        ],
      ])
      ->save();

    $this->drupalGet('/node/1');
    // @todo This might change to "Test Article" in the future, because the
    // "trim_type" is a bit confusing, see
    // https://www.drupal.org/project/smart_trim/issues/3308868.
    $session->elementTextEquals('css', 'article > div > div > div:nth-child(2) > p', 'Test');
  }

  /**
   * Tests if the token will not get cut off when trimming by words.
   */
  public function testTokenNotCutOffTrimTypeWords(): void {
    $session = $this->assertSession();
    $display_repository = \Drupal::service('entity_display.repository');
    // Editing our "filter_test" format:
    $edit = [
      'edit-filters-token-filter-status' => 1,
      'edit-filters-filter-html-escape-status' => 0,
    ];
    $this->drupalGet('admin/config/content/formats/manage/filter_test');
    $this->submitForm($edit, 'Save configuration');
    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'trim_options' => [
            'replace_tokens' => TRUE,
          ],
        ],
      ])
      ->save();

    $this->drupalGet('/node/1');
    $session->elementTextEquals('css', 'article > div > div > div:nth-child(2) > p', 'Test Article');
  }

  /**
   * Tests the Strip HTML option.
   */
  public function testStripHtml(): void {
    $session = $this->assertSession();
    $display_repository = \Drupal::service('entity_display.repository');
    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 7,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'trim_options' => [
            'text' => TRUE,
          ],
        ],
      ])
      ->save();

    $this->drupalGet('/node/2');
    $session->elementTextEquals('css', 'article > div > div > div:nth-child(2) > p', 'The situation is dire and there is');
  }

  /**
   * Tests the formatter settings summary.
   *
   * @covers \Drupal\smart_trim\Plugin\Field\FieldFormatter\SmartTrimFormatter::settingsSummary
   *
   * @test
   */
  public function testFormatterSettingsSummary(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');

    // Check summary with minimal settings and not more link.
    $display_repository->getViewDisplay('node', 'article', 'default')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 100,
          'trim_type' => 'chars',
          'more' => [
            'display_link' => FALSE,
          ],
          'trim_options' => [],
        ],
      ])
      ->save();
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertSession()->responseContains('100 characters');
    $this->assertSession()->responseNotContains('Suffix:');
    $this->assertSession()->responseNotContains('<em>More</em> link enabled');
    $this->assertSession()->responseNotContains('Only display <em>More</em> link when trimmed');
    $this->assertSession()->responseNotContains('Open <em>More</em> link in new window');
    $this->assertSession()->responseNotContains('<em>More</em> link aria-label:');
    $this->assertSession()->responseNotContains('<em>More</em> link class:');
    $this->assertSession()->responseNotContains('Strip HTML');
    $this->assertSession()->responseNotContains('Honor a zero trim length');
    $this->assertSession()->responseNotContains('Replace tokens before trimming');

    // Check summary with 15 words, a suffix, default more link settings, and
    // strip HTML checked.
    $display_repository->getViewDisplay('node', 'article', 'default')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 15,
          'trim_type' => 'words',
          'trim_suffix' => '- - -',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
          'trim_options' => ['text' => 1],
        ],
      ])
      ->save();
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertSession()->responseContains('15 words');
    $this->assertSession()->responseContains('Suffix: <em class="placeholder">- - -</em>');
    $this->assertSession()->responseContains('<em>More</em> link enabled, text: <em class="placeholder">More</em>');
    $this->assertSession()->responseNotContains('Only display <em>More</em> link when trimmed');
    $this->assertSession()->responseNotContains('Open <em>More</em> link in new window');
    $this->assertSession()->responseContains('<em>More</em> link aria-label: <em class="placeholder">Read more about [node:title]</em>');
    $this->assertSession()->responseContains('<em>More</em> link class: <em class="placeholder">more-link</em>');
    $this->assertSession()->responseContains('Strip HTML');
    $this->assertSession()->responseNotContains('Honor a zero trim length');
    $this->assertSession()->responseNotContains('Replace tokens before trimming');

    // Check summary with 99 chars, no suffix, customized more settings, and
    // honor zero length and apply tokens before checked.
    $display_repository->getViewDisplay('node', 'article', 'default')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 99,
          'trim_type' => 'chars',
          'more' => [
            'display_link' => TRUE,
            'class' => 'blah-link',
            'link_trim_only' => TRUE,
            'target_blank' => TRUE,
            'text' => 'Blah',
            'aria_label' => 'Blah blah blah [node:title]',
          ],
          'trim_options' => [
            'trim_zero' => 1,
            'replace_tokens' => 1,
          ],
        ],
      ])
      ->save();
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertSession()->responseContains('99 characters');
    $this->assertSession()->responseNotContains('Suffix:');
    $this->assertSession()->responseContains('<em>More</em> link enabled, text: <em class="placeholder">Blah</em>');
    $this->assertSession()->responseContains('Only display <em>More</em> link when trimmed');
    $this->assertSession()->responseContains('Open <em>More</em> link in new window');
    $this->assertSession()->responseContains('<em>More</em> link aria-label: <em class="placeholder">Blah blah blah [node:title]</em>');
    $this->assertSession()->responseContains('<em>More</em> link class: <em class="placeholder">blah-link</em>');
    $this->assertSession()->responseNotContains('Strip HTML');
    $this->assertSession()->responseContains('Honor a zero trim length');
    $this->assertSession()->responseContains('Replace tokens before trimming');
  }

  /**
   * Tests the more link displays when option selected.
   *
   * @test
   */
  public function testMoreLink(): void {
    $display_repository = \Drupal::service('entity_display.repository');
    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 6,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->save();

    $this->drupalGet('/node/1');
    $query = $this->xpath('//a[text() = "More"]');
    $this->assertEmpty($query, 'Expected zero "More" links.');

    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 6,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();
    $this->drupalGet('/node/1');
    $query = $this->xpath('//a[text() = "More"]');
    $this->assertCount(1, $query, 'Expected 1 "More" link.');
  }

  /**
   * Tests that when "wrap output" is selected, wrapper class is output.
   */
  public function testWrapperClass() {
    $display_repository = \Drupal::service('entity_display.repository');
    // Edit formatter settings, specify no wrapper:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'wrap_output' => FALSE,
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->save();

    // Confirm no wrapper class present.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//div[contains(@class, "trimmed")]');
    $this->assertEquals(0, count($query));

    // Find div following "Body" label.
    $query = $this->xpath('//div[text() = "Body"]/following-sibling::div');
    $this->assertEquals('Test [node:content-type]', $query[0]->getText());

    // Edit formatter settings to use wrapper div and class.
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'wrap_output' => TRUE,
          'wrap_class' => 'trimmed',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();

    // Check that wrapper class present in output.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//div[contains(@class, "trimmed")]');
    $this->assertEquals(1, count($query));
  }

  /**
   * Tests wrapper class applied to more link when present.
   */
  public function testMoreLinkWrapperClass() {
    $display_repository = \Drupal::service('entity_display.repository');
    // Edit formatter settings, specify no wrapper:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'wrap_output' => FALSE,
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => FALSE,
          ],
        ],
      ])
      ->save();

    // Confirm that when more link not shown, more wrapper not in content.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//div[contains(@class, "more-link")]');
    $this->assertEquals(0, count($query));

    // Edit formatter settings to display more link wrapper class.
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'wrap_output' => FALSE,
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();

    // Check that default more link wrapper class present in output.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//div[contains(@class, "more-link")]');
    $this->assertEquals(1, count($query));

    // Edit formatter settings with custom more link wrapper class.
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'wrap_output' => FALSE,
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-wrapper',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();

    // Check that default more link wrapper class not present.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//div[contains(@class, "more-link")]');
    $this->assertEquals(0, count($query));
    // Check that modified more link wrapper class present in output.
    $query = $this->xpath('//div[contains(@class, "more-wrapper")]');
    $this->assertEquals(1, count($query));
  }

  /**
   * Tests the more link's "open in a new window" configuration.
   */
  public function testOpenInNewWindow() {
    $display_repository = \Drupal::service('entity_display.repository');
    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 6,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => TRUE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();

    // Verify more link specifies target: _blank.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//a[text() = "More"]');
    $this->assertEquals('_blank', $query[0]->getAttribute('target'));

    // Edit formatter settings:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 10,
          'trim_type' => 'words',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => FALSE,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();
    // Verify target not present.
    $this->drupalGet('/node/1');
    $query = $this->xpath('//a[text() = "More"]');
    $this->assertNull($query[0]->getAttribute('target'));
  }

  /**
   * Tests the "More link only when content is trimmed" formatter config option.
   */
  public function testMoreLinkOnlyWhenContentIsTrimmed(): void {
    $session = $this->assertSession();
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 100,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => TRUE,
            'target_blank' => 0,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();
    $this->drupalGet('/node/1');
    $session->linkNotExists('More');

    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 5,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => TRUE,
            'target_blank' => 0,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();
    $this->drupalGet('/node/1');
    $session->linkExists('More');

    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('body', [
        'type' => 'smart_trim',
        'settings' => [
          'trim_length' => 100,
          'trim_type' => 'chars',
          'summary_handler' => 'trim',
          'more' => [
            'display_link' => TRUE,
            'class' => 'more-link',
            'link_trim_only' => FALSE,
            'target_blank' => 0,
            'text' => 'More',
            'aria_label' => 'Read more about [node:title]',
          ],
        ],
      ])
      ->save();
    $this->drupalGet('/node/1');
    $session->linkExists('More');
  }

}
