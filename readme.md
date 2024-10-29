# The Vds multiple-choice question type

The difference from the standard Moodle multiple-choice question type is the evaluation of the tests. The Creator chooses the correct answers. If there are n correct answers, the participant receives one point for each correctly selected answer, and loses one point for each incorrect answer or for each correct answer not selected. Here is an example:

Which of these animals are mammals?

    A. Dog
    B. Frog
    C. Toad
    D. Cat
    E. Cow
    F. Salamander
    G. Lion

Scoring:

    • ADEG (4 out of 4 correct) full score (4 points)
    • D (1 correct, 3 not selected) 0 points
    • ADEGF (4 correct, 1 incorrect) 3 points
    • ADEBC (3 correct, 1 not selected, 2 incorrect) 0 points
    • ADE (3 correct, 1 not selected) 2 points


## Acknowledgements

This is a multiple-choice, multiple-response question type that was created by
the Team from the CENEOS GmbH.


## Installation and set-up

### Install from the plugins database

Install from the Moodle plugins database
* https://moodle.org/plugins/...to-be-continued...

### Install using git

To install using git, type these commands in the root of your Moodle install
    git clone https://github.com/highTowerSU/moodle-qtype_vdsmultiplechoice.git question/type/vdsmultiplechoice
    echo '/question/type/vdsmultiplechoice/' >> .git/info/exclude

Then run the moodle update process
Site administration > Notifications
