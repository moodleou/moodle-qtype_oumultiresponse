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
 * Unit tests for the OU multiple response question class.
 *
 * @package   qtype_oumultiresponse
 * @copyright 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_oumultiresponse;

use qtype_oumultiresponse_question;
use test_question_maker;
use question_attempt_step;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/oumultiresponse/question.php');


/**
 * Unit tests for (some of) question/type/oumultiresponse/questiontype.php.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::replace_char_at
 * @covers ::grade_response
 * @covers ::grade_computation
 */
final class question_test extends \basic_testcase {

    /**
     * Tolerance for floating point comparisons.
     *
     * @var float
     */
    private $tolerance = 0.000001;

    public function test_replace_char_at(): void {
        $this->assertEquals(qtype_oumultiresponse_question::replace_char_at('220', 0, '0'), '020');
    }

    public function test_grade_responses_right_right(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(['choice0' => '1', 'choice2' => '1']);
        $this->assertEquals(1, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedright);
    }

    public function test_grade_responses_right(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(['choice0' => '1']);
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_wrong_wrong(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(['choice1' => '1', 'choice3' => '1']);
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedwrong);
    }

    public function test_grade_responses_right_wrong_wrong(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(
            ['choice0' => '1', 'choice1' => '1', 'choice3' => '1']);
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_wrong(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(['choice0' => '1', 'choice1' => '1']);
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_right_wrong(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response([
            'choice0' => '1', 'choice2' => '1', 'choice3' => '1']);
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_right_wrong_wrong(): void {
        $mc = test_question_maker::make_question('oumultiresponse', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response([
            'choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1']);
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_computation(): void {
        $right = new \stdClass();
        $right->fraction = 1.0;
        $wrong = new \stdClass();
        $wrong->fraction = 0.0;

        $penalty = 0.3333333;
        $answers = [$right, $right, $right, $wrong, $wrong, $wrong];

        $responsehistory = ['111', '000', '000', '000', '000', '000'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.3333333, $this->tolerance, '');

        $responsehistory = ['111', '111', '000', '000', '000', '000'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.6666667, $this->tolerance, '');

        $responsehistory = ['1', '1', '1', '0', '0', '0'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 1.0, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '111', '000', '000'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.6666667, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '111', '111', '000'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.3333333, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '111', '111', '111'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.0, $this->tolerance, '');

        $responsehistory = ['011', '000', '000', '100', '111', '111'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['001', '000', '000', '110', '111', '111'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.1111111, $this->tolerance, '');

        $responsehistory = ['111', '111', '001', '100', '010', '000'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.7777778, $this->tolerance, '');

        $responsehistory = ['100', '100', '001', '100', '011', '001'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.1111111, $this->tolerance, '');

        $responsehistory = ['101', '101', '001', '110', '011', '111'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.1111111, $this->tolerance, '');

        $responsehistory = ['011', '001', '001', '100', '110', '111'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.3333333, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '110', '110', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.4444444, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '110', '100', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.5555556, $this->tolerance, '');

        $responsehistory = ['110', '101', '101', '111', '110', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['111', '110', '110', '111', '111', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['011', '111', '110', '111', '111', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['110', '111', '110', '111', '111', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['111', '111', '111', '110', '110', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.4444444, $this->tolerance, '');

        $responsehistory = ['110', '111', '110', '111', '111', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['011', '111', '110', '111', '111', '100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.2222222, $this->tolerance, '');

        $responsehistory = ['011', '111', '110', '110', '111', '001'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.3333333, $this->tolerance, '');

        $responsehistory = ['11', '01', '01', '10', '10', '00'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 3), 0.7777778, $this->tolerance, '');

        $penalty = 0.2;
        $answers = [$right, $right, $right, $right, $wrong, $wrong, $wrong, $wrong];
        $responsehistory = [
                '11111', '10111', '11100', '11011', '10011', '01010', '01000', '00100'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 5), 0.45, $this->tolerance, '');

        $penalty = 0.33334;
        $answers = [$right, $right, $wrong, $wrong, $wrong];
        $responsehistory = ['0', '0', '1', '1', '0'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 1), 0.0, $this->tolerance, '');

        $responsehistory = ['0', '1', '1', '0', '0'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 1), 0.5, $this->tolerance, '');

        $responsehistory = ['1', '1', '0', '0', '0'];
        $this->assertEqualsWithDelta(qtype_oumultiresponse_question::grade_computation(
                $responsehistory, $answers, $penalty, 1), 1.0, $this->tolerance, '');
    }
}
