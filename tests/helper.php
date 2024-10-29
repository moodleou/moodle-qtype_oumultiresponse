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
 * Test helper code for the OU multiple response question type.
 *
 * @package    qtype_vdsmultiplechoice
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test helper class for the OU multiple response question type.
 *
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vdsmultiplechoice_test_helper {
    public function get_test_questions() {
        return array('two_of_four', 'two_of_five');
    }

    /**
     * @return qtype_vdsmultiplechoice_question
     */
    public static function make_vdsmultiplechoice_question_two_of_four() {

        question_bank::load_question_definition_classes('vdsmultiplechoice');
        $mc = new qtype_vdsmultiplechoice_question();

        test_question_maker::initialise_a_question($mc);

        $mc->name = 'OU multiple response question';
        $mc->questiontext = 'Which are the odd numbers?';
        $mc->generalfeedback = 'The odd numbers are One and Three.';
        $mc->qtype = question_bank::get_qtype('vdsmultiplechoice');

        $mc->shuffleanswers = 1;
        $mc->answernumbering = '123';
        $mc->showstandardinstruction = 0;

        test_question_maker::set_standard_combined_feedback_fields($mc);

        $mc->answers = array(
            13 => new question_answer(13, 'One', 1, 'One is odd.', FORMAT_HTML),
            14 => new question_answer(14, 'Two', 0, 'Two is even.', FORMAT_HTML),
            15 => new question_answer(15, 'Three', 1, 'Three is odd.', FORMAT_HTML),
            16 => new question_answer(16, 'Four', 0, 'Four is even.', FORMAT_HTML),
        );

        $mc->hints = array(
            new qtype_vdsmultiplechoice_hint(1, 'Hint 1.', FORMAT_HTML, true, false, false),
            new qtype_vdsmultiplechoice_hint(2, 'Hint 2.', FORMAT_HTML, true, true, true),
        );

        return $mc;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options, for
     * the question returned by {@link make_an_vdsmultiplechoice_two_of_four()}.
     * @return object
     */
    public static function get_vdsmultiplechoice_question_data_two_of_four() {
        global $USER;

        $qdata = new stdClass();
        $qdata->id = 0;
        $qdata->contextid = 0;
        $qdata->category = 0;
        $qdata->parent = 0;
        $qdata->stamp = make_unique_id_code();
        $qdata->version = make_unique_id_code();
        $qdata->timecreated = time();
        $qdata->timemodified = time();
        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'vdsmultiplechoice';
        $qdata->name = 'OU multiple response question';
        $qdata->questiontext = 'Which are the odd numbers?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The odd numbers are One and Three.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;
        $qdata->idnumber = '';

        $qdata->options = new stdClass();
        $qdata->options->shuffleanswers = 1;
        $qdata->options->answernumbering = '123';
        $qdata->options->showstandardinstruction = 0;
        $qdata->options->correctfeedback =
                test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback =
                test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback =
                test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => 'One',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 1,
                'feedback' => 'One is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => 'Two',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => 'Two is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => 'Three',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 1,
                'feedback' => 'Three is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => 'Four',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => 'Four is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'id' => 1,
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 0,
                'options' => 0,
            ),
            2 => (object) array(
                'id' => 2,
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 1,
            ),
        );

        return $qdata;
    }

    /**
     * @return qtype_vdsmultiplechoice_question
     */
    public static function make_vdsmultiplechoice_question_two_of_five() {
        question_bank::load_question_definition_classes('vdsmultiplechoice');
        $mc = new qtype_vdsmultiplechoice_question();

        test_question_maker::initialise_a_question($mc);

        $mc->name = 'OU multiple response three of five';
        $mc->questiontext = 'The answer is A, B and C';
        $mc->generalfeedback = '';
        $mc->qtype = question_bank::get_qtype('vdsmultiplechoice');

        $mc->shuffleanswers = false;
        $mc->answernumbering = 'none';
        $mc->showstandardinstruction = 0;

        test_question_maker::set_standard_combined_feedback_fields($mc);

        $mc->answers = array(
            13 => new question_answer(13, 'A', 1, '', FORMAT_HTML),
            14 => new question_answer(14, 'B', 1, '', FORMAT_HTML),
            15 => new question_answer(15, 'C', 0, '', FORMAT_HTML),
            16 => new question_answer(16, 'D', 0, '', FORMAT_HTML),
            17 => new question_answer(17, 'E', 0, '', FORMAT_HTML),
        );

        $mc->hints = array(
            1 => new qtype_vdsmultiplechoice_hint(1, 'Hint 1.', FORMAT_HTML, true, false, false),
            2 => new qtype_vdsmultiplechoice_hint(2, 'Hint 2.', FORMAT_HTML, true, true, true),
        );

        return $mc;
    }

    /**
     * @return stdClass date to create an vdsmultiplechoice question.
     */
    public function get_vdsmultiplechoice_question_form_data_two_of_four() {
        $fromform = new stdClass();

        $fromform->name = 'OU multiple response question';
        $fromform->questiontext = array('text' => 'Which are the odd numbers?', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'The odd numbers are One and Three.', 'format' => FORMAT_HTML);
        $fromform->shuffleanswers = 0;
        $fromform->answernumbering = 'abc';
        $fromform->showstandardinstruction = 0;
        $fromform->answer = array(
                0 => array('text' => 'One', 'format' => FORMAT_PLAIN),
                1 => array('text' => 'Two', 'format' => FORMAT_PLAIN),
                2 => array('text' => 'Three', 'format' => FORMAT_PLAIN),
                3 => array('text' => 'Four', 'format' => FORMAT_PLAIN)
        );
        $fromform->correctanswer = array(
                0 => 1,
                1 => 0,
                2 => 1,
                3 => 0
        );
        $fromform->feedback = array(
                0 => array('text' => 'One is odd.', 'format' => FORMAT_HTML),
                1 => array('text' => 'Two is even.', 'format' => FORMAT_HTML),
                2 => array('text' => 'Three is odd.', 'format' => FORMAT_HTML),
                3 => array('text' => 'Four is odd.', 'format' => FORMAT_HTML)
        );
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;

        return $fromform;
    }
}
