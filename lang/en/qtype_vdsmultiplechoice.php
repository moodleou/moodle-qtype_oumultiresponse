<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * VdS multiple choice question type language strings.
 *
 * @package    qtype_vdsmultiplechoice
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['choices'] = 'Available choices';
$string['combinedcontrolnamevdsmultiplechoice'] = 'check box group';
$string['correctanswer'] = 'Correct';
$string['err_correctanswerblank'] = 'You have marked this choice as correct but it is blank!';
$string['err_nonecorrect'] = 'You have not marked any choices as correct.';
$string['err_youneedmorechoices'] = 'You need to enter two or more choices.';
$string['notenoughcorrectanswers'] = 'You must select at least one correct choice';
$string['pluginname'] = 'VdS multiple choice';
$string['pluginname_help'] = 'A multiple-choice, multiple-response question type with particular scoring rules.';
$string['pluginname_link'] = 'question/type/vdsmultiplechoice';
$string['pluginnameadding'] = 'Adding an VdS multiple choice question';
$string['pluginnameediting'] = 'Editing an VdS multiple choice question';
$string['pluginnamesummary'] = '<p>A multiple-choice, multiple-response question type with particular scoring rules.</p>
<p>Recommended if your question has more than one correct answer.</p>';
$string['privacy:metadata'] = 'Multiple response question type plugin allows question authors to set default options as user preferences.';
$string['privacy:preference:defaultmark'] = 'The default mark set for a given question.';
$string['privacy:preference:penalty'] = 'The penalty for each incorrect try when questions are run using the \'Interactive with multiple tries\' or \'Adaptive mode\' behaviour.';
$string['privacy:preference:shuffleanswers'] = 'Whether the answers should be automatically shuffled.';
$string['privacy:preference:answernumbering'] = 'Which numbering stye should be used (1., 2., 3., .../a., b., c., ... etc.)';
$string['privacy:preference:showstandardinstruction'] = 'Whether showing standard instruction.';
$string['toomanyoptions'] = 'You have selected too many options.';
$string['showeachanswerfeedback'] = 'Show the feedback for the selected responses.';
$string['yougotnright'] = 'You have correctly selected {$a->num} options.';
$string['yougot1right'] = 'You have correctly selected one option.';
$string['showstandardinstruction'] = 'Show standard instruction';
$string['showstandardinstruction_help'] = 'With this setting enabled, standard instruction will be supplied as part of the selection area (e.g. "Select one or more:"). If disabled, question authors can instead included instructions in the question content, if required.';
