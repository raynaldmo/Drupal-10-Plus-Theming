<?php

namespace Drupal\Tests\smart_trim\Unit;

use Drupal\smart_trim\TruncateHTML;
use Drupal\Tests\UnitTestCase;

/**
 * Unit Test coverage.
 *
 * @coversDefaultClass \Drupal\smart_trim\TruncateHTML
 *
 * @group smart_trim
 */
class TruncateHTMLTest extends UnitTestCase {

  /**
   * Testing truncateChars.
   *
   * @covers ::truncateChars
   *
   * @dataProvider truncateCharsDataProvider
   */
  public function testTruncateChars($html, $limit, $ellipsis, $expected) {
    $truncate = new TruncateHTML();
    $this->assertSame($expected, $truncate->truncateChars($html, $limit, $ellipsis));
  }

  /**
   * Data provider for testTruncateChars().
   */
  public function truncateCharsDataProvider(): array {
    return [
      [
        'A test string',
        5,
        '…',
        'A…',
      ],
      [
        '“I like funky quotes”',
        5,
        '',
        '“I',
      ],
      [
        '“I <em>really, really</em> like funky quotes”',
        14,
        '',
        '“I <em>really</em>',
      ],
      [
        'Armenian character test Հ from issue 3334442',
        25,
        '',
        'Armenian character test Հ',
      ],
    ];
  }

  /**
   * Covers TruncateWords.
   *
   * @covers ::truncateWords
   *
   * @dataProvider truncateWordsDataProvider
   */
  public function testTruncateWords($html, $limit, $ellipsis, $expected): void {
    $truncate = new TruncateHTML();
    $this->assertSame($expected, $truncate->truncateWords($html, $limit, $ellipsis));
  }

  /**
   * Data provider for testTruncateWords().
   */
  public function truncateWordsDataProvider(): array {
    return [
      [
        'A test string',
        2,
        '…',
        'A test…',
      ],
      [
        'A test string',
        3,
        '…',
        'A test string',
      ],
      [
        '“I like funky quotes”',
        2,
        '',
        '“I like',
      ],
      [
        '“I like funky quotes”',
        4,
        '',
        '“I like funky quotes”',
      ],
      [
        '“I <em>really, really</em> like funky quotes”',
        2,
        '',
        '“I <em>really</em>',
      ],
      [
        '<p><strong>Every <em>man <s>who has lotted here over the centuries, has looked up</s> to the light</em> and imagined climbing to freedom.</strong></p>',
        10,
        '',
        '<p><strong>Every <em>man <s>who has lotted here over the centuries, has</s></em></strong></p>',
      ],
    ];
  }

}
