<?php

namespace Drupal\Tests\smart_trim\Unit;

use Drupal\Tests\UnitTestCase;

require __DIR__ . "/../../../smart_trim.module";

/**
 * Unit Test coverage.
 *
 * @group smart_trim
 */
class SuggestionTest extends UnitTestCase {

  /**
   * Testing hook_theme_suggestions_HOOK_alter.
   *
   * @covers smart_trim_theme_suggestions_smart_trim_alter
   *
   * @dataProvider suggestionProvider
   */
  public function testSuggestion($variables, $expected_count, $expected_samples) {
    $suggestions = [];
    smart_trim_theme_suggestions_smart_trim_alter($suggestions, $variables);
    // Confirm that expected number of suggestions received.
    $this->assertSame(count($suggestions), $expected_count);
    // Confirm that each of the expected suggestions is in the result.
    foreach ($expected_samples as $sample) {
      $this->assertContains($sample, $suggestions);
    }
  }

  /**
   * Data provider for testSuggestion().
   */
  public function suggestionProvider(): array {
    return [
      [
        [
          'entity_type' => 'node',
          'entity_bundle' => 'article',
          'field' => 'body',
        ],
        7,
        [
          'smart_trim__node',
          'smart_trim__node__body',
          'smart_trim__node__article__body',
        ],
      ],
      [
        [
          'entity_type' => 'node',
          'entity_bundle' => 'page',
          'field' => 'description',
        ],
        7,
        [
          'smart_trim__node',
          'smart_trim__node__description',
          'smart_trim__node__page__description',
        ],
      ],
      [
        [
          'entity_type' => 'node',
          'entity_bundle' => NULL,
          'field' => 'body',
        ],
        3,
        [
          'smart_trim__node',
          'smart_trim__body',
          'smart_trim__node__body',
        ],
      ],
      [
        [
          'entity_type' => 'node',
          'field' => 'body',
        ],
        3,
        [
          'smart_trim__node',
          'smart_trim__body',
          'smart_trim__node__body',
        ],
      ],
      [
        [
          'entity_type' => 'user',
          'entity_bundle' => 'user',
          'field' => 'biography',
        ],
        7,
        [
          'smart_trim__user',
          'smart_trim__user__biography',
          'smart_trim__user__user__biography',
        ],
      ],
      [
        [
          'entity_type' => NULL,
          'entity_bundle' => NULL,
          'field' => NULL,
        ],
        0,
        [],
      ],
      [
        [],
        0,
        [],
      ],
    ];
  }

}
