# Change log for the OU Multi-response question type

## Changes in 2.5
* This version works with Moodle 5.0.
* Automation test failures are fixed.
* Cherry-picked commits since february 2024 till now:
  * Update btn to "Save preview options and start again"
  * Fix backup and restore tests to run synchronously M4.4
  * Choice Tiny Editor disrupts the theme column layout on edit question page
  * Add required for answer field when user not submit an answer
* Upgrade the CI to support Moodle 5.0 (PHP 8.3), and update the branch to support branch MOODLE_405_STABLE, and MOODLE_500_STABLE.

## Changes in 2.4

* This version works with Moodle 4.0.


## Changes in 2.3

* Option added to hide the 'Select one or more:' message.
* Fix layout to match recent changes in Moodle core multiple choice layout
* ... including when used in combined questions.


## Changes in 2.2

* Change for when OU Multi-response question subquestions
  are used inside combined questions, so that the question
  authoring can control 'Number the choices' setting.


## Changes in 2.1

* Support for the Moodle mobile app.
* Update Behat tests to work with Moodle 3.8.


## Changes in 2.0

* Fix positioning of the right/wrong icons in Moodle 3.5+.
* Fix automated tests to pass with Moodle 3.6.


## Changes in 1.9

* Travis-CI automated testing integration.
* Privacy API implementation.
* Better wording in the question type chooser to help distinguish this
  from the standard Moodle multiple choice type.
* Fix some coding style.
* Due to privacy API support, this version now only works in Moodle 3.4+
  For older Moodles, you will need to use a previous version of this plugin.


## 1.8 and before

Changes were not documented.
