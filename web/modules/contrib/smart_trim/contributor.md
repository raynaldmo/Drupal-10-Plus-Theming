# Contributing
First off, thanks for taking the time to contribute!

#### Table Of Contents

[Code of Conduct](#code-of-conduct)

[Get an Answer to a Question](#get-an-answer-to-a-question)

[Prerequisites](#prerequisites)
  * [Drupal](#drupal)
  * [Patterns and Components](#patterns-and-components)

[How Can I Contribute?](#how-can-i-contribute)
  * [Reporting Bugs](#reporting-bugs)
  * [Suggesting Enhancements](#suggesting-enhancements)
  * [Your First Code Contribution](#your-first-code-contribution)
  * [Local Development](#local-development)

[Styleguides](#styleguides)
  * [Git Commit Messages](#git-commit-messages)
  * [JavaScript Styleguide](#javascript-styleguide)
  * [PHP Styleguide](#php-styleguide)

## Code of Conduct
Please follow the [Drupal Code of Conduct](https://www.drupal.org/dcoc).

## Get an Answer to a Question

> **Note:** Please don't file an issue to ask a question. You'll get faster
results by using the resources below.

You can join the Drupal Slack team:

* [Join the Drupal Slack Team](https://drupalslack.com/)

    * For questions about Smart Trim - use #contribute.

## Prerequisites

### Drupal
Smart Trim  is first and foremost a Drupal module. See the [Getting Involved Guide](https://www.drupal.org/contribute/development) for a step-by-step for contributing to Drupal.

### Patterns and Components
Smart Trim implements a new field formatter for text fields (text, text_long,
and text_with_summary, if you want to get technical) that improves upon the
"Summary or Trimmed" formatter built into Drupal core.

## How Can I Contribute?
### Reporting Bugs

This section guides you through submitting a bug report. Following these
guidelines helps maintainers and the community understand your report, reproduce
the behavior, and find related reports.

Bugs are tracked as [Drupal.org issues](https://www.drupal.org/project/smart_trim).

**Note:** If you find a **Closed** issue that seems like it is the same thing
that you're experiencing, open a new issue and include a link to the original
issue in the body of your new one.

#### Before Submitting A Bug Report

* **Perform a cursory search on [Drupal.org](https://www.drupal.org/project/issues/smart_trim?categories=All)**
to see if the problem has already been reported. If it has and the issue is
still open, add a comment to the existing issue instead of opening a new one.

#### How Do I Submit A (Good) Bug Report?

Explain the problem and include additional details to help maintainers
reproduce the problem:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many
details as possible. For example, start by explaining how you installed
Smart Trim, e.g. which command exactly you used in the terminal. When
listing steps, **don't just say what you did, but explain how you did it**.
For example, if you moved the cursor to the end of a line, explain
if you used the mouse, or a keyboard shortcut, and if so which one?
* **Provide specific examples to demonstrate the steps**. Include links to
files or GitHub projects, or copy/pasteable snippets, which you use in
those examples. If you're providing snippets in the issue, use[Markdowncode blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the behavior you observed after following the steps** and
point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why.**
* **Include screenshots and animated GIFs** which show you following the
described steps and clearly demonstrate the problem. You can use
[this tool](https://www.cockos.com/licecap/) to record GIFs on macOS and
Windows, and [this tool](https://github.com/colinkeenan/silentcast) or
[this tool](https://github.com/GNOME/byzanz) on Linux.
* **If you're reporting that Smart Trim crashed**, include a crash report
with a stack trace from PHP.  Include the crash report in the issue in a
\<pre\>\<code\> block, an attachment, or put it in a [gist](https://gist.github.com/) and provide a link to that gist.
* **For performance or memory-related problems**, include a
[profile capture](https://blackfire.io/) with your report.
* **If the problem wasn't triggered by a specific action**, describe what
you were doing before the problem happened and share more information
using the guidelines below.

Provide more context by answering these questions:

* **Did the problem start happening recently** (e.g. after updating to a
new version) or was this always a problem?
* If the problem started happening recently, can you reproduce the problem
in an older version? What's the most recent version in which the problem
doesn't happen?
* **Can you reliably reproduce the issue?** If not, provide details about
how often the problem happens and under which conditions it normally
happens.
* For files-system related files (e.g. opening and editing files),
**does the problem happen for all files and projects or only some?** Does
the problem happen only when working with local or remote files (e.g. on
network drives), with files of a specific type (e.g. only JavaScript or
Python files), with large files or files with very long lines, or with
files in a specific encoding? Is there anything else special about the
files you are using?

Include details about your configuration and environment:

* **Which version are you using?**
* **What's the name and version of the OS you're using**?
* **Which Drupal modules do you have installed?** You can get that list by
running `drush pm-list`.

### Suggesting Enhancements

This section guides you through submitting an enhancement suggestion,
including completely new features and minor improvements to existing
functionality.

Following these guidelines helps maintainers and the community understand
your suggestion and find related suggestions.

Before creating enhancement suggestions, please check this list
(#before-submitting-an-enhancement-suggestion) as you might find out that
you don't need to create one. When you are creating an enhancement
suggestion, please [include as many details as possible]
(#how-do-i-submit-a-good-enhancement-suggestion). Include the steps that
you imagine you would take if the feature you're requesting existed.

#### Before Submitting An Enhancement Suggestion

* **Check if there's already [a module](https://drupal.org/project/modules)
which provides that enhancement.**

* **Perform a cursory search on [Drupal.org](https://www.drupal.org/project/issues/smart_trim?categories=All)** to see if the enhancement has
already been suggested. If it has, add a comment to the existing issue instead
of opening a new one.

#### How Do I Submit A (Good) Enhancement Suggestion?

Enhancement suggestions are tracked as [Drupal.org issues](https://www.drupal.org/project/issues/smart_trim?categories=All). Create an issue and
provide the following information:

* **Use a clear and descriptive title** for the issue to identify the
suggestion.
* **Provide a step-by-step description of the suggested enhancement** in
as many details as possible.
* **Provide specific examples to demonstrate the steps**. Include copy/
pasteable snippets which you use in those examples, as \<code\> blocks.
* **Describe the current behavior** and explain which behavior you
expected to see instead and why.
* **Include screenshots and animated GIFs** which help you demonstrate the
steps or point out the part which the suggestion is related to. You can
use [this tool](https://www.cockos.com/licecap/) to record GIFs on macOS
and Windows, and [this tool](https://github.com/colinkeenan/silentcast) or
[this tool](https://github.com/GNOME/byzanz) on Linux.
* **Explain why this enhancement would be useful** to most users and isn't
something that can or should be implemented as another module.
* **List other text editors or applications where this enhancement exists.**
* **Specify which version you're using.**
* **Specify the name and version of the OS you're using.**

### Your First Code Contribution

Unsure where to begin contributing? You can start by looking through issues
marked "novice":

* [Novice issues](https://www.drupal.org/project/issues/search?issue_tags=Novice)
- issues which should only require a few lines of code, and a test or two.

#### Local development

You can install the Smart Trim module as you would normally install a
contributed Drupal module.
[Visit Drupal Installing Modules](https://www.drupal.org/node/1897420) for
further information.

### Styleguides

#### Git Commit Messages

[Use the Drupal.org Commit Guidelines](https://www.drupal.org/node/52287)
(even if the module maintainers don't!)

#### JavaScript Styleguide

[Use the Drupal.org JS Coding Standards](https://www.drupal.org/docs/develop/standards/javascript/javascript-coding-standards).

#### PHP Styleguide

- [Use the Drupal.org PHP Coding Standards](https://www.drupal.org/docs/develop/standards/coding-standards).
- [Run Coder Sniffer](https://www.drupal.org/docs/contributed-modules/code-review-module/installing-coder-sniffer).
