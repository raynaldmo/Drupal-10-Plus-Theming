<?php

namespace Drupal\smart_trim;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;

/**
 * @file
 * Contains trim functionality.
 *
 * As noted on http://www.pjgalbraith.com/2011/11/truncating-text-html-with-php/
 * with some modifications to adhere to the Drupal Coding Standards.
 */

/**
 * This class basically truncates the HTML characters.
 */
class TruncateHTML {

  /**
   * Total characters.
   *
   * @var int
   */
  protected int $charCount = 0;

  /**
   * Total words.
   *
   * @var int
   */
  protected int $wordCount = 0;

  /**
   * Character / Word limit.
   *
   * @var int
   */
  protected int $limit;

  /**
   * Element to start on.
   *
   * @var \DOMElement
   */
  protected \DOMElement $startNode;

  /**
   * Ellipsis character.
   *
   * @var string
   */
  protected string $ellipsis;

  /**
   * Did we find the breakpoint?
   *
   * @var bool
   */
  protected bool $foundBreakpoint = FALSE;

  /**
   * Sets up object for use.
   *
   * @param string $html
   *   Text to be prepared.
   * @param int $limit
   *   Amount of text to return.
   * @param string $ellipsis
   *   Characters to use at the end of the text.
   *
   * @return \DOMDocument
   *   Prepared DOMDocument to work with.
   */
  protected function init(string $html, int $limit, string $ellipsis): \DOMDocument {
    $dom = Html::load($html);

    // The body tag node, our html fragment is automatically wrapped in
    // a <html><body> etc.
    $this->startNode = $dom->getElementsByTagName("body")->item(0);
    $this->limit = $limit;
    $this->ellipsis = $ellipsis;
    $this->charCount = 0;
    $this->wordCount = 0;
    $this->foundBreakpoint = FALSE;

    return $dom;
  }

  /**
   * Truncates HTML text by characters.
   *
   * @param string $html
   *   Text to be updated.
   * @param int $limit
   *   Amount of text to allow.
   * @param string $ellipsis
   *   Characters to use at the end of the text.
   *
   * @return string
   *   Resulting text.
   */
  public function truncateChars(string $html, int $limit, string $ellipsis = '...'): string {
    $html = trim($html);

    if ($limit <= 0 || $limit >= mb_strlen(strip_tags($html))) {
      return $html;
    }
    $dom = $this->init($html, $limit, $ellipsis);
    // Pass the body node on to be processed.
    $this->domNodeTruncateChars($this->startNode);
    return Html::serialize($dom);
  }

  /**
   * Truncates HTML text by words.
   *
   * @param string $html
   *   Text to be updated.
   * @param int $limit
   *   Amount of text to allow.
   * @param string $ellipsis
   *   Characters to use at the end of the text.
   *
   * @return string
   *   Resulting text.
   */
  public function truncateWords(string $html, int $limit, string $ellipsis = '...'): string {
    $html = trim($html);

    if ($limit <= 0 || $limit >= $this->countWords(strip_tags($html))) {
      return $html;
    }

    $dom = $this->init($html, $limit, $ellipsis);
    // Pass the body node on to be processed.
    $this->domNodeTruncateWords($this->startNode);
    return Html::serialize($dom);
  }

  /**
   * Truncates a DOMNode by character count.
   *
   * @param \DOMNode $domnode
   *   Object to be truncated.
   */
  protected function domNodeTruncateChars(\DOMNode $domnode): void {
    foreach ($domnode->childNodes as $node) {

      if ($this->foundBreakpoint) {
        return;
      }

      if ($node->hasChildNodes()) {
        $this->domNodeTruncateChars($node);
      }
      else {
        $text = html_entity_decode($node->nodeValue, ENT_QUOTES, 'UTF-8');
        $length = mb_strlen($text);
        if (($this->charCount + $length) >= $this->limit) {
          // We have found our end point.
          $node->nodeValue = Unicode::truncate($text, $this->limit - $this->charCount, TRUE);
          $this->removeTrailingPunctuation($node);
          $this->removeProceedingNodes($node);
          $this->insertEllipsis($node);
          $this->foundBreakpoint = TRUE;
          return;
        }
        else {
          $this->charCount += $length;
        }
      }
    }
  }

  /**
   * Truncates a DOMNode by words.
   *
   * @param \DOMNode $domnode
   *   Object to be truncated.
   */
  protected function domNodeTruncateWords(\DOMNode $domnode): void {
    foreach ($domnode->childNodes as $node) {

      if ($this->foundBreakpoint) {
        return;
      }

      if ($node->hasChildNodes()) {
        $this->domNodeTruncateWords($node);
      }
      else {
        $cur_count = $this->countWords($node->nodeValue);

        if (($this->wordCount + $cur_count) >= $this->limit) {
          // We have found our end point.
          if ($cur_count > 1 && ($this->limit - $this->wordCount) < $cur_count) {
            // Note that PREG_SPLIT_OFFSET_CAPTURE and UTF-8 is interesting.
            // preg_split() works on the string as an array of bytes therefore
            // in order to use its results we need to use non unicode aware
            // functions.
            // @see https://bugs.php.net/bug.php?id=67487
            $words = preg_split("/[\n\r\t ]+/", $node->nodeValue, ($this->limit - $this->wordCount) + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
            end($words);
            $last_word = prev($words);
            $node->nodeValue = substr($node->nodeValue, 0, $last_word[1] + strlen($last_word[0]));
          }

          $this->removeTrailingPunctuation($node);
          $this->removeProceedingNodes($node);
          $this->insertEllipsis($node);
          $this->foundBreakpoint = TRUE;
          return;
        }
        else {
          $this->wordCount += $cur_count;
        }
      }
    }
  }

  /**
   * Removes certain punctuation from the end of the node value.
   *
   * @param \DOMNode $domnode
   *   Node to be altered.
   */
  protected function removeTrailingPunctuation(\DOMNode $domnode): void {
    while (preg_match('/[\.,:;\?!…]$/u', $domnode->nodeValue)) {
      $domnode->nodeValue = mb_substr($domnode->nodeValue, 0, -1);
    }
  }

  /**
   * Removes preceding sibling node.
   *
   * @param \DOMNode $domnode
   *   Node to be altered.
   */
  protected function removeProceedingNodes(\DOMNode $domnode): void {
    $nextnode = $domnode->nextSibling;

    if ($nextnode !== NULL) {
      $this->removeProceedingNodes($nextnode);
      $domnode->parentNode->removeChild($nextnode);
    }
    else {
      // Scan upwards till we find a sibling.
      $curnode = $domnode->parentNode;
      while ($curnode !== $this->startNode) {
        if ($curnode->nextSibling !== NULL) {
          $curnode = $curnode->nextSibling;
          $this->removeProceedingNodes($curnode);
          $curnode->parentNode->removeChild($curnode);
          break;
        }
        $curnode = $curnode->parentNode;
      }
    }
  }

  /**
   * Inserts the ellipsis character to the node.
   *
   * @param \DOMNode $domnode
   *   Node to be altered.
   */
  protected function insertEllipsis(\DOMNode $domnode): void {
    // HTML tags to avoid appending the ellipsis to.
    $avoid = ['a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'];

    if (in_array($domnode->parentNode->nodeName, $avoid) && ($domnode->parentNode->parentNode !== NULL || $domnode->parentNode->parentNode !== $this->startNode)) {
      // Append as text node to parent instead.
      $textnode = new \DOMText($this->ellipsis);

      if ($domnode->parentNode->parentNode->nextSibling) {
        $domnode->parentNode->parentNode->insertBefore($textnode, $domnode->parentNode->parentNode->nextSibling);
      }
      else {
        $domnode->parentNode->parentNode->appendChild($textnode);
      }
    }
    else {
      // Append to current node.
      $domnode->nodeValue = rtrim($domnode->nodeValue) . $this->ellipsis;
    }
  }

  /**
   * Gets number of words in text.
   *
   * @param string $text
   *   Text to be counted.
   *
   * @return int
   *   Results
   */
  protected function countWords(string $text): int {
    $words = preg_split("/[\n\r\t ]+/", $text, -1, PREG_SPLIT_NO_EMPTY);
    return count($words);
  }

}
