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

require_once($CFG->dirroot . '/question/type/multichoice/question.php');


/**
 * VdS multiple choice question definition class.
 *
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vdsmultiplechoice_question extends qtype_multichoice_multi_question
        implements question_automatically_gradable_with_countback {

    /**
     * @var int standard instruction to be displayed if enabled.
     */
    public $showstandardinstruction = 0;

    /**
     *  Set renderer for ou multiple response
     *
     * @param moodle_page $page
     * @return renderer_base
     */
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_vdsmultiplechoice');
    }

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        if ($preferredbehaviour == 'interactive') {
            return question_engine::make_behaviour(
                    'interactivecountback', $qa, $preferredbehaviour);
        }
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }

    public function classify_response(array $response) {
        $choices = parent::classify_response($response);
        $numright = $this->get_num_correct_choices();
        foreach ($choices as $choice) {
            $choice->fraction /= $numright;
        }
        return $choices;
    }

    public function grade_response(array $response) {
        list($numright, $total) = $this->get_num_parts_right($response);
        $numcorrect = $this->get_num_correct_choices();
        $numwrong = $this->get_num_selected_choices($response) - $numright;
        $nummissed = $numcorrect - $numright;

        $fraction = ($numright - $numwrong - $nummissed ) / $numcorrect;
        if($fraction < 0) {
            $fraction = 0;
        }

        $state = question_state::graded_state_for_fraction($fraction);
        if ($state == question_state::$gradedwrong && $numright > 0) {
            $state = question_state::$gradedpartial;
        }

        return array($fraction, $state);
    }

    protected function disable_hint_settings_when_too_many_selected(
            question_hint_with_parts $hint) {
        parent::disable_hint_settings_when_too_many_selected($hint);
        $hint->showchoicefeedback = false;
    }

    public function compute_final_grade($responses, $totaltries) {
        $result = parent::compute_final_grade($responses, $totaltries);
        return $result;
    }

    public static function replace_char_at($string, $pos, $newchar) {
        return substr($string, 0, $pos) . $newchar . substr($string, $pos + 1);
    }
}
