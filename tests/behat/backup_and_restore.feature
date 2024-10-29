@ou @ou_vle @qtype @qtype_vdsmultiplechoice
Feature: Test duplicating a quiz containing an OU multiple response question
  As a teacher
  In order re-use my courses containing OU multiple response questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype           | name                | template    |
      | Test questions   | vdsmultiplechoice | vdsmultiplechoice 001 | two_of_four |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | vdsmultiplechoice 001 | 1 |

  @javascript
  Scenario: Backup and restore a course containing an OU multiple response question
    When I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "vdsmultiplechoice 001" in the question bank
    Then the following fields match these values:
      | Question name                      | vdsmultiplechoice 001                |
      | Question text                      | Which are the odd numbers?         |
      | General feedback                   | The odd numbers are One and Three. |
      | Default mark                       | 1                                  |
      | Shuffle                            | 0                                  |
      | Choice 1                           | One                                |
      | Choice 2                           | Two                                |
      | Choice 3                           | Three                              |
      | Choice 4                           | Four                               |
      | id_correctanswer_0                 | 1                                  |
      | id_correctanswer_1                 | 0                                  |
      | id_correctanswer_2                 | 1                                  |
      | id_correctanswer_3                 | 0                                  |
      | For any correct response           | Well done!                         |
      | For any partially correct response | Parts, but only parts, of your response are correct. |
      | For any incorrect response         | That is not right at all.          |
