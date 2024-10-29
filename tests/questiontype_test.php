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
 * Unit tests for the OU multiple response question type class.
 *
 * @package    qtype_vdsmultiplechoice
 * @copyright  2024 CENEOS GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_vdsmultiplechoice;

use qtype_vdsmultiplechoice;
use test_question_maker;
use question_possible_response;
use question_answer;
use question_check_specified_fields_expectation;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/vdsmultiplechoice/questiontype.php');


/**
 * Unit tests for (some of) question/type/vdsmultiplechoice/questiontype.php.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questiontype_test extends \question_testcase {
    /**
     * @var qtype_vdsmultiplechoice
     */
    private $qtype;

    public function setUp(): void {
        $this->qtype = new qtype_vdsmultiplechoice();
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEquals(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'vdsmultiplechoice');
    }

    public function test_initialise_question_instance() {
        $qdata = test_question_maker::get_question_data('vdsmultiplechoice', 'two_of_four');
        $expectedq = test_question_maker::make_question('vdsmultiplechoice', 'two_of_four');
        $qdata->stamp = $expectedq->stamp;
        $qdata->version = $expectedq->version;
        $qdata->timecreated = $expectedq->timecreated;
        $qdata->timemodified = $expectedq->timemodified;

        $question = $this->qtype->make_question($qdata);

        $this->assertEquals($expectedq, $question);
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_possible_responses() {
        $q = new \stdClass();
        $q->id = 1;
        $q->options = new \stdClass();
        $q->options->answers = [
            1 => (object) array('answer' => 'frog', 'fraction' => 1),
            2 => (object) array('answer' => 'toad', 'fraction' => 1),
            3 => (object) array('answer' => 'newt', 'fraction' => 0),
        ];
        $responses = $this->qtype->get_possible_responses($q);

        $this->assertEquals(array(
            1 => array(1 => new question_possible_response('frog', 0.5)),
            2 => array(2 => new question_possible_response('toad', 0.5)),
            3 => array(3 => new question_possible_response('newt', 0)),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_random_guess_score() {
        $questiondata = new \stdClass();
        $questiondata->options = new \stdClass();
        $questiondata->options->answers = array(
            1 => new question_answer(1, 'A', 1, '', FORMAT_HTML),
            2 => new question_answer(2, 'B', 0, '', FORMAT_HTML),
            3 => new question_answer(3, 'C', 0, '', FORMAT_HTML),
        );
        $this->assertEquals(1 / 3,
                $this->qtype->get_random_guess_score($questiondata), '', 0.000001);

        $questiondata->options->answers[2]->fraction = 1;
        $this->assertEquals(2 / 3,
                $this->qtype->get_random_guess_score($questiondata), '', 0.000001);

        $questiondata->options->answers[4] = new question_answer(4, 'D', 0, '', FORMAT_HTML);
        $this->assertEquals(1 / 2,
                $this->qtype->get_random_guess_score($questiondata), '', 0.000001);
    }

    public function test_xml_import() {
        $xml = '  <question type="vdsmultiplechoice">
    <name>
      <text>OU multiple response question</text>
    </name>
    <questiontext format="html">
      <text>Which are the odd numbers?</text>
    </questiontext>
    <generalfeedback>
      <text>General feedback.</text>
    </generalfeedback>
    <defaultgrade>6</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <answernumbering>abc</answernumbering>
    <shuffleanswers>true</shuffleanswers>
    <showstandardinstruction>0</showstandardinstruction>
    <correctfeedback>
      <text>Well done.</text>
    </correctfeedback>
    <partiallycorrectfeedback>
      <text>Not entirely.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text>Completely wrong!</text>
    </incorrectfeedback>
    <answer fraction="100">
      <text>One</text>
      <feedback>
        <text>Specific feedback to correct answer.</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>Two</text>
      <feedback>
        <text>Specific feedback to wrong answer.</text>
      </feedback>
    </answer>
    <answer fraction="100">
      <text>Three</text>
      <feedback>
        <text>Specific feedback to correct answer.</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>Four</text>
      <feedback>
        <text>Specific feedback to wrong answer.</text>
      </feedback>
    </answer>
    <hint>
      <text>Try again.</text>
      <shownumcorrect />
    </hint>
    <hint>
      <text>Hint 2.</text>
      <shownumcorrect />
      <clearwrong />
      <options>1</options>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new \qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'vdsmultiplechoice');

        $expectedq = new \stdClass();
        $expectedq->qtype = 'vdsmultiplechoice';
        $expectedq->name = 'OU multiple response question';
        $expectedq->questiontext = 'Which are the odd numbers?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'General feedback.';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 6;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->shuffleanswers = 1;
        $expectedq->correctfeedback = array('text' => 'Well done.',
                'format' => FORMAT_HTML);
        $expectedq->partiallycorrectfeedback = array('text' => 'Not entirely.',
                'format' => FORMAT_HTML);
        $expectedq->shownumcorrect = false;
        $expectedq->incorrectfeedback = array('text' => 'Completely wrong!',
                'format' => FORMAT_HTML);

        $expectedq->answer = array(
            array('text' => 'One', 'format' => FORMAT_HTML),
            array('text' => 'Two', 'format' => FORMAT_HTML),
            array('text' => 'Three', 'format' => FORMAT_HTML),
            array('text' => 'Four', 'format' => FORMAT_HTML),
        );
        $expectedq->correctanswer = array(1, 0, 1, 0);
        $expectedq->feedback = array(
            array('text' => 'Specific feedback to correct answer.',
                    'format' => FORMAT_HTML),
            array('text' => 'Specific feedback to wrong answer.',
                    'format' => FORMAT_HTML),
            array('text' => 'Specific feedback to correct answer.',
                    'format' => FORMAT_HTML),
            array('text' => 'Specific feedback to wrong answer.',
                    'format' => FORMAT_HTML),
        );

        $expectedq->hint = array(
                array('text' => 'Try again.', 'format' => FORMAT_HTML),
                array('text' => 'Hint 2.', 'format' => FORMAT_HTML));
        $expectedq->hintshownumcorrect = array(true, true);
        $expectedq->hintclearwrong = array(false, true);
        $expectedq->hintshowchoicefeedback = array(false, true);

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
        $this->assertEquals($expectedq->answer, $q->answer);
    }

    public function test_xml_import_legacy() {
        $xml = '  <question type="vdsmultiplechoice">
    <name>
      <text>008 OUMR feedback test</text>
    </name>
    <questiontext format="html">
      <text>&lt;p&gt;OUMR question.&lt;/p&gt; &lt;p&gt;Right answers are eighta ' .
                'and eightb.&lt;/p&gt;</text>
    </questiontext>
    <image></image>
    <generalfeedback>
      <text>General feedback.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.33</penalty>
    <hidden>0</hidden>
    <shuffleanswers>1</shuffleanswers>
    <answernumbering>abc</answernumbering>
    <answer>
      <correctanswer>1</correctanswer>
      <text>eighta</text>
      <feedback>
        <text>&lt;p&gt;Specific feedback to correct answer.&lt;/p&gt;</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>1</correctanswer>
      <text>eightb</text>
      <feedback>
        <text>&lt;p&gt;Specific feedback to correct answer.&lt;/p&gt;</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>one</text>
      <feedback>
        <text>&lt;p&gt;Specific feedback to wrong answer.&lt;/p&gt;</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>two</text>
      <feedback>
        <text>&lt;p&gt;Specific feedback to wrong answer.&lt;/p&gt;</text>
      </feedback>
    </answer>
    <correctfeedback>
      <text>Correct overall feedback</text>
    </correctfeedback>
    <correctresponsesfeedback>0</correctresponsesfeedback>
    <partiallycorrectfeedback>
      <text>Partially correct overall feedback.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text>Incorrect overall feedback.</text>
    </incorrectfeedback>
    <unlimited>0</unlimited>
    <penalty>0.33</penalty>
    <hint>
      <statenumberofcorrectresponses>0</statenumberofcorrectresponses>
      <showfeedbacktoresponses>1</showfeedbacktoresponses>
      <clearincorrectresponses>0</clearincorrectresponses>
      <hintcontent>
        <text>Hint 1.</text>
      </hintcontent>
    </hint>
    <hint>
      <statenumberofcorrectresponses>0</statenumberofcorrectresponses>
      <showfeedbacktoresponses>1</showfeedbacktoresponses>
      <clearincorrectresponses>0</clearincorrectresponses>
      <hintcontent>
        <text>Hint 2.</text>
      </hintcontent>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new \qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'vdsmultiplechoice');

        $expectedq = new \stdClass();
        $expectedq->qtype = 'vdsmultiplechoice';
        $expectedq->name = '008 OUMR feedback test';
        $expectedq->questiontext = '<p>OUMR question.</p><p>Right answers are ' .
                'eighta and eightb.</p>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'General feedback.';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->shuffleanswers = 1;
        $expectedq->answernumbering = 'abc';
        $expectedq->correctfeedback = array('text' => 'Correct overall feedback',
                'format' => FORMAT_HTML);
        $expectedq->partiallycorrectfeedback = array(
                'text' => 'Partially correct overall feedback.',
                'format' => FORMAT_HTML);
        $expectedq->shownumcorrect = false;
        $expectedq->incorrectfeedback = array('text' => 'Incorrect overall feedback.',
                'format' => FORMAT_HTML);

        $expectedq->answer = array(
            array('text' => 'eighta', 'format' => FORMAT_HTML),
            array('text' => 'eightb', 'format' => FORMAT_HTML),
            array('text' => 'one', 'format' => FORMAT_HTML),
            array('text' => 'two', 'format' => FORMAT_HTML));
        $expectedq->correctanswer = array(1, 1, 0, 0);
        $expectedq->feedback = array(
            array('text' => '<p>Specific feedback to correct answer.</p>',
                    'format' => FORMAT_HTML),
            array('text' => '<p>Specific feedback to correct answer.</p>',
                    'format' => FORMAT_HTML),
            array('text' => '<p>Specific feedback to wrong answer.</p>',
                    'format' => FORMAT_HTML),
            array('text' => '<p>Specific feedback to wrong answer.</p>',
                    'format' => FORMAT_HTML),
        );

        $expectedq->hint = array(
                array('text' => 'Hint 1.', 'format' => FORMAT_HTML),
                array('text' => 'Hint 2.', 'format' => FORMAT_HTML));
        $expectedq->hintshownumcorrect = array(false, false);
        $expectedq->hintclearwrong = array(false, false);
        $expectedq->hintshowchoicefeedback = array(true, true);

        $this->assertEquals($expectedq->answer, $q->answer);
        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_xml_export() {
        $qdata = test_question_maker::get_question_data('vdsmultiplechoice', 'two_of_four');
        $qdata->defaultmark = 6;

        $exporter = new \qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 0  -->
  <question type="vdsmultiplechoice">
    <name>
      <text>OU multiple response question</text>
    </name>
    <questiontext format="html">
      <text>Which are the odd numbers?</text>
    </questiontext>
    <generalfeedback format="html">
      <text>The odd numbers are One and Three.</text>
    </generalfeedback>
    <defaultgrade>6</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <idnumber></idnumber>
    <shuffleanswers>true</shuffleanswers>
    <answernumbering>123</answernumbering>
    <showstandardinstruction>0</showstandardinstruction>
    <correctfeedback format="html">
      <text>Well done!</text>
    </correctfeedback>
    <partiallycorrectfeedback format="html">
      <text>Parts, but only parts, of your response are correct.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="html">
      <text>That is not right at all.</text>
    </incorrectfeedback>
    <shownumcorrect/>
    <answer fraction="100" format="plain_text">
      <text>One</text>
      <feedback format="html">
        <text>One is odd.</text>
      </feedback>
    </answer>
    <answer fraction="0" format="plain_text">
      <text>Two</text>
      <feedback format="html">
        <text>Two is even.</text>
      </feedback>
    </answer>
    <answer fraction="100" format="plain_text">
      <text>Three</text>
      <feedback format="html">
        <text>Three is odd.</text>
      </feedback>
    </answer>
    <answer fraction="0" format="plain_text">
      <text>Four</text>
      <feedback format="html">
        <text>Four is even.</text>
      </feedback>
    </answer>
    <hint format="html">
      <text>Hint 1.</text>
      <shownumcorrect/>
    </hint>
    <hint format="html">
      <text>Hint 2.</text>
      <shownumcorrect/>
      <clearwrong/>
      <options>1</options>
    </hint>
  </question>
';

        // Hack so the test passes in both 3.5 and 3.6.
        if (strpos($xml, 'idnumber') === false) {
            $expectedxml = str_replace("    <idnumber></idnumber>\n", '', $expectedxml);
        }

        $this->assert_same_xml($expectedxml, $xml);
    }
}
