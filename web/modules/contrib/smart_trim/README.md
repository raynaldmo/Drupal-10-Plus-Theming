# Smart Trim

Smart Trim implements a new field formatter for textfields (text, text_long, and
text_with_summary, if you want to get technical) that improves upon the "Summary
or Trimmed" formatter built into Drupal.

For a full description of the module, visit the [project page](https://www.drupal.org/project/smart_trim) or the [documentation](https://www.drupal.org/docs/contributed-modules/smart-trim).

Submit bug reports and feature suggestions, or track changes in the [issue queue](https://www.drupal.org/project/issues/smart_trim).

* To submit bug reports and feature suggestions, or to track changes visit:
   [issue queue](https://www.drupal.org/project/issues/smart_trim)

## Requirements

Drupal contrib modules

* [Token](https://www.drupal.org/project/token)

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Configuration

1. Navigate to Administration > Extend and enable the module.
1. Navigate to /admin/structure/types/manage/article/display/teaser
   * Or any fieldable entity with a text, text_long, or text_with_summary field
   * Or any display mode. Typically teaser is trimmed text.
1. In the format of the text field, select _Smart trimmed_
1. Click the configuration wheel on the far right
1. Update Smart Trim formatter configuration as desired. Configuration options
include:
   * Trim by number of characters or words.
   * Customize the "More" link.

## Maintainers

* Mark Casias - [markie](https://www.drupal.org/u/markie)
* Michael Anello - [ultimike](https://www.drupal.org/u/ultimike)
* AmyJune Hineline - [volkswagenchick](https://www.drupal.org/u/volkswagenchick)

### Supporting organizations

* [Kanopi Studios](https://www.drupal.org/kanopi-studios)
* [DrupalEasy](https://www.drupal.org/drupaleasy)
