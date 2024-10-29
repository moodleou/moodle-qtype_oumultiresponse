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
 * VdS multiple choice question definition class.
 *
 * @package    qtype_vdsmultiplechoice
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks file access for vdsmultiplechoice questions.
 */
function qtype_vdsmultiplechoice_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_vdsmultiplechoice', $filearea, $args, $forcedownload, $options);
}
