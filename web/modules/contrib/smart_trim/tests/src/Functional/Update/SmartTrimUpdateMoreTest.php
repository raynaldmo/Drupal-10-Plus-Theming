<?php

namespace Drupal\Tests\smart_trim\Functional\Update;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;

/**
 * Tests update functions for the Block Content module.
 *
 * @group block_content
 */
class SmartTrimUpdateMoreTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../../fixtures/update/drupal-10.0.8-smart_trim-2.0.php.gz',
    ];
  }

  /**
   * Tests update hook moves 'More' settings into more array.
   *
   * @test
   */
  public function testUpdateMoreSettings(): void {
    $adminUser = $this->drupalCreateUser();
    $adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $adminUser->save();
    $this->drupalLogin($adminUser);

    $this->runUpdates();

    $display_repository = \Drupal::service('entity_display.repository');
    $body = $display_repository->getViewDisplay('node', 'article', 'teaser')->getComponent('body');
    // Check that the more settings are in the more array.
    $this->assertEquals('smart_trim', $body['type']);
    $this->assertTrue($body['settings']['more']['display_link']);
    $this->assertFalse($body['settings']['more']['link_trim_only']);
    $this->assertFalse($body['settings']['more']['target_blank']);
    // Database preconfigured with non-default text values to verify.
    $this->assertEquals('Blah', $body['settings']['more']['text']);
    $this->assertEquals('Blah blah blah [node:title]', $body['settings']['more']['aria_label']);
    $this->assertEquals('blah-link', $body['settings']['more']['class']);
    // Check that the legacy settings have been removed.
    $this->assertArrayNotHasKey('more_link', $body['settings']);
    $this->assertArrayNotHasKey('more_text', $body['settings']);
    $this->assertArrayNotHasKey('more_aria_label', $body['settings']);
    $this->assertArrayNotHasKey('more_class', $body['settings']);
  }

}
