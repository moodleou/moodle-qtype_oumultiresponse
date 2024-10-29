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


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/multichoice/questiontype.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');


/**
 * This questions type is like the standard multiplechoice question type, but
 * with these differences:
 *
 * 1. The focus is just on the multiple response case.
 *
 * 2. The correct answer is just indicated on the editing form by a indicating
 * which choices are correct. There is no complex but flexible scoring system.
 *
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vdsmultiplechoice extends question_type {
    public function has_html_answers() {
        return true;
    }

    public function requires_qtypes() {
        return array('multichoice');
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_vdsmultiplechoice',
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('shuffleanswers', $fromform->shuffleanswers);
        $this->set_default_value('answernumbering', $fromform->answernumbering);
        $this->set_default_value('showstandardinstruction', $fromform->showstandardinstruction);
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass();

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // The following hack to checks that at least two answers exist.
        $answercount = 0;
        foreach ($question->answer as $key => $answer) {
            if ($answer != '') {
                $answercount++;
            }
        }
        if ($answercount < 2) { // Check there are at lest 2 answers for multiple choice.
            $result->notice = get_string('notenoughanswers', 'qtype_multichoice', '2');
            return $result;
        }

        // Insert all the new answers.
        $answers = array();
        foreach ($question->answer as $key => $answerdata) {
            if (trim($answerdata['text']) == '') {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            $answer->answer = $this->import_or_save_files($answerdata,
                    $context, 'question', 'answer', $answer->id);
            $answer->answerformat = $answerdata['format'];
            $answer->fraction = !empty($question->correctanswer[$key]);
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];

            $DB->update_record('question_answers', $answer);
            $answers[] = $answer->id;
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $options = $DB->get_record('question_vdsmultiplechoice',
                array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->showstandardinstruction = 0;
            $options->id = $DB->insert_record('question_vdsmultiplechoice', $options);
        }

        $options->answernumbering = $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;
        $options->showstandardinstruction = !empty($question->showstandardinstruction);
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_vdsmultiplechoice', $options);

        $this->save_hints($question, true);
    }

    public function save_hints($formdata, $withparts = false) {
        global $DB;
        $context = $formdata->context;

        $oldhints = $DB->get_records('question_hints',
                array('questionid' => $formdata->id), 'id ASC');

        if (!empty($formdata->hint)) {
            $numhints = max(array_keys($formdata->hint)) + 1;
        } else {
            $numhints = 0;
        }

        if ($withparts) {
            if (!empty($formdata->hintclearwrong)) {
                $numclears = max(array_keys($formdata->hintclearwrong)) + 1;
            } else {
                $numclears = 0;
            }
            if (!empty($formdata->hintshownumcorrect)) {
                $numshows = max(array_keys($formdata->hintshownumcorrect)) + 1;
            } else {
                $numshows = 0;
            }
            $numhints = max($numhints, $numclears, $numshows);
        }

        if (!empty($formdata->hintshowchoicefeedback)) {
            $numshowfeedbacks = max(array_keys($formdata->hintshowchoicefeedback)) + 1;
        } else {
            $numshowfeedbacks = 0;
        }
        $numhints = max($numhints, $numshowfeedbacks);

        for ($i = 0; $i < $numhints; $i += 1) {
            if (html_is_blank($formdata->hint[$i]['text'])) {
                $formdata->hint[$i]['text'] = '';
            }

            if ($withparts) {
                $clearwrong = !empty($formdata->hintclearwrong[$i]);
                $shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
            }

            $showchoicefeedback = !empty($formdata->hintshowchoicefeedback[$i]);

            if (empty($formdata->hint[$i]['text']) && empty($clearwrong) &&
                    empty($shownumcorrect) && empty($showchoicefeedback)) {
                continue;
            }

            // Update an existing hint if possible.
            $hint = array_shift($oldhints);
            if (!$hint) {
                $hint = new stdClass();
                $hint->questionid = $formdata->id;
                $hint->hint = '';
                $hint->id = $DB->insert_record('question_hints', $hint);
            }

            $hint->hint = $this->import_or_save_files($formdata->hint[$i],
                    $context, 'question', 'hint', $hint->id);
            $hint->hintformat = $formdata->hint[$i]['format'];
            if ($withparts) {
                $hint->clearwrong = $clearwrong;
                $hint->shownumcorrect = $shownumcorrect;
            }
            $hint->options = $showchoicefeedback;
            $DB->update_record('question_hints', $hint);
        }

        // Delete any remaining old hints.
        $fs = get_file_storage();
        foreach ($oldhints as $oldhint) {
            $fs->delete_area_files($context->id, 'question', 'hint', $oldhint->id);
            $DB->delete_records('question_hints', array('id' => $oldhint->id));
        }
    }

    protected function make_hint($hint) {
        return qtype_vdsmultiplechoice_hint::load_from_record($hint);
    }

    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function make_answer($answer) {
        // Overridden just so we can make it public for use by question.php.
        return parent::make_answer($answer);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->shuffleanswers = $questiondata->options->shuffleanswers;
        $question->answernumbering = $questiondata->options->answernumbering;
        $question->showstandardinstruction = $questiondata->options->showstandardinstruction;
        $this->initialise_combined_feedback($question, $questiondata, true);
        $this->initialise_question_answers($question, $questiondata, false);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_vdsmultiplechoice', array('questionid' => $questionid));
        return parent::delete_question($questionid, $contextid);
    }

    protected function get_num_correct_choices($questiondata) {
        $numright = 0;
        foreach ($questiondata->options->answers as $answer) {
            if (!question_state::graded_state_for_fraction($answer->fraction)->is_incorrect()) {
                $numright += 1;
            }
        }
        return $numright;
    }

    public function get_random_guess_score($questiondata) {
        // We compute the randome guess score here on the assumption we are using
        // the deferred feedback behaviour, and the question text tells the
        // student how many of the responses are correct.
        // Amazingly, the forumla for this works out to be
        // # correct choices / total # choices in all cases.
        return $this->get_num_correct_choices($questiondata) /
                count($questiondata->options->answers);
    }

    public function get_possible_responses($questiondata) {
        $numright = $this->get_num_correct_choices($questiondata);
        $parts = array();

        foreach ($questiondata->options->answers as $aid => $answer) {
            $parts[$aid] = array($aid =>
                    new question_possible_response($answer->answer, $answer->fraction / $numright));
        }

        return $parts;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'vdsmultiplechoice') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'vdsmultiplechoice';

        $question->shuffleanswers = $format->trans_single(
                $format->getpath($data, array('#', 'shuffleanswers', 0, '#'), 1));
        $question->answernumbering = $format->getpath($data,
                array('#', 'answernumbering', 0, '#'), 'abc');
        $question->showstandardinstruction = $format->getpath($data,
            array('#', 'showstandardinstruction', 0, '#'), 1);

        $format->import_combined_feedback($question, $data, true);

        // Run through the answers.
        $answers = $data['#']['answer'];
        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer, true,
                    $format->get_format($question->questiontextformat));
            $question->answer[] = $ans->answer;
            $question->correctanswer[] = !empty($ans->fraction);
            $question->feedback[] = $ans->feedback;

            // Backwards compatibility.
            if (array_key_exists('correctanswer', $answer['#'])) {
                $keys = array_keys($question->correctanswer);
                $question->correctanswer[end($keys)] = $format->getpath($answer,
                        array('#', 'correctanswer', 0, '#'), 0);
            }
        }

        $format->import_hints($question, $data, true, true,
                $format->get_format($question->questiontextformat));

        // Get extra choicefeedback setting from each hint.
        if (!empty($question->hintoptions)) {
            foreach ($question->hintoptions as $key => $options) {
                $question->hintshowchoicefeedback[$key] = !empty($options);
            }
        }

        return $question;
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $output = '';

        $output .= "    <shuffleanswers>" . $format->get_single(
                $question->options->shuffleanswers) . "</shuffleanswers>\n";
        $output .= "    <answernumbering>{$question->options->answernumbering}</answernumbering>\n";
        $output .= "    <showstandardinstruction>{$question->options->showstandardinstruction}</showstandardinstruction>\n";

        $output .= $format->write_combined_feedback($question->options,
                                                    $question->id,
                                                    $question->contextid);
        $output .= $format->write_answers($question->options->answers);

        return $output;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid, true);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);

        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'correctfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'partiallycorrectfeedback', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'question', 'incorrectfeedback', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid, true);
        $this->delete_files_in_hints($questionid, $contextid);
        $fs->delete_area_files($contextid, 'question', 'correctfeedback', $questionid);
        $fs->delete_area_files($contextid, 'question', 'partiallycorrectfeedback', $questionid);
        $fs->delete_area_files($contextid, 'question', 'incorrectfeedback', $questionid);
    }
}


/**
 * An extension of {@link question_hint_with_parts} for oumultirespone questions
 * with an extra option for whether to show the feedback for each choice.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vdsmultiplechoice_hint extends question_hint_with_parts {
    /** @var boolean whether to show the feedback for each choice. */
    public $showchoicefeedback;

    /**
     * Constructor.
     * @param string $hint The hint text
     * @param bool $shownumcorrect whether the number of right parts should be shown
     * @param bool $clearwrong whether the wrong parts should be reset.
     * @param bool $showchoicefeedback whether to show the feedback for each choice.
     */
    public function __construct($id, $hint, $hintformat, $shownumcorrect,
            $clearwrong, $showchoicefeedback) {
        parent::__construct($id, $hint, $hintformat, $shownumcorrect, $clearwrong);
        $this->showchoicefeedback = $showchoicefeedback;
    }

    /**
     * Create a basic hint from a row loaded from the question_hints table in the database.
     * @param object $row with $row->hint, ->shownumcorrect and ->clearwrong set.
     * @return question_hint_with_parts
     */
    public static function load_from_record($row) {
        return new qtype_vdsmultiplechoice_hint($row->id, $row->hint, $row->hintformat,
                $row->shownumcorrect, $row->clearwrong, !empty($row->options));
    }

    public function adjust_display_options(question_display_options $options) {
        parent::adjust_display_options($options);
        if (defined('qtype_multichoice::COMBINED_BUT_NOT_CHOICE_FEEDBACK')) {
            // Newer Moodle versions/
            if ($options->feedback && !$this->showchoicefeedback) {
                $options->feedback = qtype_multichoice::COMBINED_BUT_NOT_CHOICE_FEEDBACK;
            }
        } else {
            // Older Moodle versions.
            $options->suppresschoicefeedback = !$this->showchoicefeedback;
        }
    }
}
