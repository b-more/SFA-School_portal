<?php

namespace App\Console\Commands;

use App\Models\ClassSection;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QuizzesLoadStarter extends Command
{
    protected $signature = 'quizzes:load-starter
        {--dry-run : Show what would be created without writing anything}
        {--force : Re-create even if a starter quiz with the same title already exists for the section}';

    protected $description = 'Seed grade-appropriate starter quizzes for every active class section so teachers and learners have content immediately.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $sections = ClassSection::with('grade')->where('is_active', true)->get();
        $totalQuizzes = 0; $totalQuestions = 0; $skipped = 0;

        foreach ($sections as $section) {
            $grade = $section->grade;
            if (!$grade) { continue; }

            $teacherId = $section->class_teacher_id ?: $this->fallbackTeacherId();
            if (!$teacherId) {
                $this->warn("No teacher available for section #{$section->id}; skipping.");
                continue;
            }

            $bundles = $this->bundlesForLevel((int) ($grade->level ?? 0));

            foreach ($bundles as $bundle) {
                $title = $bundle['title'];
                $exists = Quiz::where('class_section_id', $section->id)->where('title', $title)->exists();
                if ($exists && !$force) {
                    $skipped++;
                    continue;
                }

                $subjectId = $bundle['subject_name']
                    ? $this->resolveSubjectId($bundle['subject_name'], (int) ($grade->level ?? 0))
                    : null;
                $totalPoints = collect($bundle['questions'])->sum(fn ($q) => $q['points'] ?? 1);

                $this->line(sprintf(
                    "%s %s · %s · %d questions",
                    $dry ? '[dry]' : '[seed]',
                    $grade->name . ' ' . $section->name,
                    $title,
                    count($bundle['questions'])
                ));

                if ($dry) {
                    $totalQuizzes++;
                    $totalQuestions += count($bundle['questions']);
                    continue;
                }

                DB::transaction(function () use ($section, $teacherId, $bundle, $subjectId, $totalPoints, $title, $force, &$totalQuizzes, &$totalQuestions) {
                    if ($force) {
                        Quiz::where('class_section_id', $section->id)->where('title', $title)->each(fn ($q) => $q->delete());
                    }
                    $quiz = Quiz::create([
                        'title' => $title,
                        'description' => $bundle['description'] ?? null,
                        'assigned_by' => $teacherId,
                        'class_section_id' => $section->id,
                        'subject_id' => $subjectId,
                        'grade_id' => $section->grade_id,
                        'time_limit_minutes' => null,
                        'total_points' => $totalPoints,
                        'shuffle_questions' => true,
                        'status' => 'published',
                        'due_at' => null,
                    ]);
                    foreach ($bundle['questions'] as $qi => $q) {
                        $question = $quiz->questions()->create([
                            'question_text' => $q['q'],
                            'type' => $q['type'] ?? 'mcq',
                            'points' => $q['points'] ?? 1,
                            'position' => $qi,
                        ]);
                        foreach ($q['options'] as $oi => $opt) {
                            $question->options()->create([
                                'option_text' => $opt[0],
                                'is_correct' => $opt[1] === true,
                                'position' => $oi,
                            ]);
                        }
                    }
                    $totalQuizzes++;
                    $totalQuestions += count($bundle['questions']);
                });
            }
        }

        $this->info(sprintf(
            "%s — quizzes=%d questions=%d skipped=%d (already present)",
            $dry ? 'Dry run' : 'Done',
            $totalQuizzes, $totalQuestions, $skipped
        ));
        return self::SUCCESS;
    }

    private function fallbackTeacherId(): ?int
    {
        return Teacher::where('is_active', true)->orderBy('id')->value('id');
    }

    private function resolveSubjectId(string $name, int $level): ?int
    {
        $isSecondary = $level >= 11;
        $q = Subject::where('name', $name)->where('is_active', true);
        if ($isSecondary) {
            $found = (clone $q)->where('grade_level', 'Secondary')->value('id');
            if ($found) return $found;
        } else {
            $found = (clone $q)->where('grade_level', 'Primary')->value('id');
            if ($found) return $found;
        }
        return Subject::where('name', $name)->where('is_active', true)->value('id');
    }

    private function bundlesForLevel(int $level): array
    {
        if ($level <= 3) return $this->preschoolBundles();
        if ($level <= 6) return $this->lowerPrimaryBundles();
        if ($level <= 10) return $this->upperPrimaryBundles();
        if ($level <= 12) return $this->juniorSecondaryBundles();
        return $this->seniorSecondaryBundles();
    }

    private function preschoolBundles(): array
    {
        return [[
            'title' => 'Welcome Quiz: Colours, Numbers & Shapes',
            'description' => 'A fun starter quiz. Pick the right answer for each picture-friendly question.',
            'subject_name' => null,
            'questions' => [
                ['q' => 'What colour is the sun?', 'options' => [['Yellow', true], ['Blue', false], ['Green', false]]],
                ['q' => 'Which number comes after 2?', 'options' => [['1', false], ['3', true], ['5', false]]],
                ['q' => 'A ball is shaped like a…', 'options' => [['Square', false], ['Circle', true], ['Triangle', false]]],
                ['q' => 'How many fingers are on one hand?', 'options' => [['3', false], ['5', true], ['10', false]]],
                ['q' => 'Which one is a fruit?', 'options' => [['Apple', true], ['Chair', false], ['Car', false]]],
                ['q' => 'What sound does a dog make?', 'options' => [['Moo', false], ['Woof', true], ['Meow', false]]],
                ['q' => 'Which is bigger?', 'options' => [['An elephant', true], ['A mouse', false], ['An ant', false]]],
            ],
        ]];
    }

    private function lowerPrimaryBundles(): array
    {
        return [
            [
                'title' => 'Starter Quiz: Maths Basics',
                'description' => 'Counting, addition and subtraction.',
                'subject_name' => 'Mathematics',
                'questions' => [
                    ['q' => 'What is 2 + 3?', 'options' => [['4', false], ['5', true], ['6', false]]],
                    ['q' => 'What is 10 - 4?', 'options' => [['5', false], ['6', true], ['7', false]]],
                    ['q' => 'What number is between 7 and 9?', 'options' => [['6', false], ['8', true], ['10', false]]],
                    ['q' => 'How many sides does a triangle have?', 'options' => [['3', true], ['4', false], ['5', false]]],
                    ['q' => 'What is 5 + 5?', 'options' => [['9', false], ['10', true], ['11', false]]],
                    ['q' => 'Which number is the largest?', 'options' => [['8', false], ['12', true], ['7', false]]],
                    ['q' => 'What is half of 8?', 'options' => [['2', false], ['4', true], ['6', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: English Basics',
                'description' => 'Letters, words and reading.',
                'subject_name' => 'English Language',
                'questions' => [
                    ['q' => 'Which letter comes after B?', 'options' => [['A', false], ['C', true], ['D', false]]],
                    ['q' => 'Choose the correct spelling.', 'options' => [['Catt', false], ['Cat', true], ['Kat', false]]],
                    ['q' => 'A baby dog is called a…', 'options' => [['Kitten', false], ['Puppy', true], ['Calf', false]]],
                    ['q' => 'Which word means a place to sleep?', 'options' => [['Bed', true], ['Pen', false], ['Cup', false]]],
                    ['q' => 'Which is a question word?', 'options' => [['Run', false], ['What', true], ['Happy', false]]],
                    ['q' => 'How many vowels are in the word APPLE?', 'options' => [['1', false], ['2', true], ['3', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: Science Basics',
                'description' => 'Plants, animals and weather.',
                'subject_name' => 'Science',
                'questions' => [
                    ['q' => 'Plants need ___ to grow.', 'options' => [['Soda', false], ['Water', true], ['Salt', false]]],
                    ['q' => 'Which of these can fly?', 'options' => [['Fish', false], ['Bird', true], ['Snake', false]]],
                    ['q' => 'The sun gives us…', 'options' => [['Rain', false], ['Light and heat', true], ['Sound', false]]],
                    ['q' => 'Which is a mammal?', 'options' => [['Frog', false], ['Cow', true], ['Crocodile', false]]],
                    ['q' => 'We breathe in…', 'options' => [['Oxygen', true], ['Petrol', false], ['Smoke', false]]],
                    ['q' => 'Ice is water that is…', 'options' => [['Boiled', false], ['Frozen', true], ['Mixed with sugar', false]]],
                ],
            ],
        ];
    }

    private function upperPrimaryBundles(): array
    {
        return [
            [
                'title' => 'Starter Quiz: Mathematics',
                'description' => 'Number operations, fractions and measurement.',
                'subject_name' => 'Mathematics',
                'questions' => [
                    ['q' => 'What is 12 x 4?', 'options' => [['44', false], ['48', true], ['52', false]]],
                    ['q' => 'What is 144 ÷ 12?', 'options' => [['11', false], ['12', true], ['14', false]]],
                    ['q' => 'Half of 1/2 is…', 'options' => [['1/4', true], ['1/3', false], ['1', false]]],
                    ['q' => 'How many minutes in 2 hours?', 'options' => [['60', false], ['90', false], ['120', true]]],
                    ['q' => 'A square has all sides…', 'options' => [['Equal', true], ['Different', false], ['Curved', false]]],
                    ['q' => 'What is 0.5 as a fraction?', 'options' => [['1/2', true], ['1/4', false], ['1/5', false]]],
                    ['q' => 'The perimeter of a square with side 5 cm is…', 'options' => [['10 cm', false], ['20 cm', true], ['25 cm', false]]],
                    ['q' => '15% of 200 is…', 'options' => [['15', false], ['30', true], ['50', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: English',
                'description' => 'Grammar, vocabulary and reading.',
                'subject_name' => 'English Language',
                'questions' => [
                    ['q' => 'Choose the noun: "She read a book."', 'options' => [['She', false], ['read', false], ['book', true]]],
                    ['q' => 'Which is the past tense of "go"?', 'options' => [['Goed', false], ['Went', true], ['Gone', false]]],
                    ['q' => 'Pick the correctly spelled word.', 'options' => [['Recieve', false], ['Receive', true], ['Receeve', false]]],
                    ['q' => 'A synonym for "happy" is…', 'options' => [['Sad', false], ['Joyful', true], ['Angry', false]]],
                    ['q' => 'Which sentence is a question?', 'options' => [['She is home.', false], ['Is she home?', true], ['She is home!', false]]],
                    ['q' => 'The opposite of "begin" is…', 'options' => [['Start', false], ['End', true], ['Open', false]]],
                    ['q' => 'Which is a pronoun?', 'options' => [['Quickly', false], ['She', true], ['Run', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: Science',
                'description' => 'Life, matter and energy.',
                'subject_name' => 'Science',
                'questions' => [
                    ['q' => 'The process by which plants make food is called…', 'options' => [['Respiration', false], ['Photosynthesis', true], ['Germination', false]]],
                    ['q' => 'Water boils at…', 'options' => [['50°C', false], ['100°C', true], ['200°C', false]]],
                    ['q' => 'Which organ pumps blood?', 'options' => [['Liver', false], ['Heart', true], ['Lung', false]]],
                    ['q' => 'Which planet do we live on?', 'options' => [['Mars', false], ['Earth', true], ['Venus', false]]],
                    ['q' => 'A material that lets electricity pass through is…', 'options' => [['Insulator', false], ['Conductor', true], ['Plastic', false]]],
                    ['q' => 'The state of matter with a fixed shape is…', 'options' => [['Solid', true], ['Liquid', false], ['Gas', false]]],
                    ['q' => 'Animals that eat only plants are called…', 'options' => [['Carnivores', false], ['Herbivores', true], ['Omnivores', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: Social Studies',
                'description' => 'Zambia, geography and citizenship.',
                'subject_name' => 'Social Studies',
                'questions' => [
                    ['q' => 'The capital city of Zambia is…', 'options' => [['Ndola', false], ['Lusaka', true], ['Kitwe', false]]],
                    ['q' => 'Zambia\'s flag has how many colours (excluding the bird)?', 'options' => [['3', false], ['4', true], ['5', false]]],
                    ['q' => 'Which river forms part of Zambia\'s southern border?', 'options' => [['Nile', false], ['Zambezi', true], ['Congo', false]]],
                    ['q' => 'Which is a continent?', 'options' => [['Africa', true], ['Zambia', false], ['Lusaka', false]]],
                    ['q' => 'The Victoria Falls is shared with which country?', 'options' => [['Malawi', false], ['Zimbabwe', true], ['Angola', false]]],
                    ['q' => 'Who is the head of state in Zambia?', 'options' => [['President', true], ['Mayor', false], ['Judge', false]]],
                ],
            ],
        ];
    }

    private function juniorSecondaryBundles(): array
    {
        return [
            [
                'title' => 'Starter Quiz: Mathematics',
                'description' => 'Algebra, geometry and arithmetic.',
                'subject_name' => 'Mathematics',
                'questions' => [
                    ['q' => 'Solve for x: 2x + 3 = 11.', 'options' => [['x = 3', false], ['x = 4', true], ['x = 5', false]]],
                    ['q' => 'The area of a rectangle 6 cm by 4 cm is…', 'options' => [['10 cm²', false], ['24 cm²', true], ['20 cm²', false]]],
                    ['q' => 'What is √81?', 'options' => [['7', false], ['9', true], ['11', false]]],
                    ['q' => 'Sum of angles in a triangle is…', 'options' => [['90°', false], ['180°', true], ['360°', false]]],
                    ['q' => 'Which is a prime number?', 'options' => [['9', false], ['11', true], ['15', false]]],
                    ['q' => '0.25 as a percentage is…', 'options' => [['2.5%', false], ['25%', true], ['250%', false]]],
                    ['q' => 'Simplify: 3a + 5a.', 'options' => [['8a', true], ['15a', false], ['2a', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: English',
                'description' => 'Grammar, comprehension and vocabulary.',
                'subject_name' => 'English',
                'questions' => [
                    ['q' => 'Identify the verb: "The boy kicked the ball."', 'options' => [['boy', false], ['kicked', true], ['ball', false]]],
                    ['q' => 'Which sentence uses correct punctuation?', 'options' => [['where are you', false], ['Where are you?', true], ['Where are you.', false]]],
                    ['q' => 'A word that describes a noun is a…', 'options' => [['Verb', false], ['Adjective', true], ['Adverb', false]]],
                    ['q' => 'The plural of "child" is…', 'options' => [['Childs', false], ['Children', true], ['Childes', false]]],
                    ['q' => 'A passage written to give information is called…', 'options' => [['A poem', false], ['An expository text', true], ['A play', false]]],
                    ['q' => 'Which is a conjunction?', 'options' => [['Run', false], ['And', true], ['Tall', false]]],
                    ['q' => 'Antonym of "ancient" is…', 'options' => [['Old', false], ['Modern', true], ['Past', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: Science',
                'description' => 'Biology, chemistry and physics fundamentals.',
                'subject_name' => 'Science',
                'questions' => [
                    ['q' => 'The chemical symbol for water is…', 'options' => [['CO₂', false], ['H₂O', true], ['O₂', false]]],
                    ['q' => 'Which organ filters blood?', 'options' => [['Lung', false], ['Kidney', true], ['Skin', false]]],
                    ['q' => 'Force is measured in…', 'options' => [['Newtons', true], ['Kilograms', false], ['Litres', false]]],
                    ['q' => 'The cell is the basic unit of…', 'options' => [['A machine', false], ['Life', true], ['Sound', false]]],
                    ['q' => 'Light travels in…', 'options' => [['Curves', false], ['Straight lines', true], ['Circles', false]]],
                    ['q' => 'An acid turns litmus paper…', 'options' => [['Blue', false], ['Red', true], ['Yellow', false]]],
                    ['q' => 'Which gas do plants release during photosynthesis?', 'options' => [['Carbon dioxide', false], ['Oxygen', true], ['Nitrogen', false]]],
                ],
            ],
        ];
    }

    private function seniorSecondaryBundles(): array
    {
        return [
            [
                'title' => 'Starter Quiz: Mathematics',
                'description' => 'Algebra, geometry, statistics.',
                'subject_name' => 'Mathematics',
                'questions' => [
                    ['q' => 'If f(x) = 2x + 3, what is f(4)?', 'options' => [['9', false], ['11', true], ['14', false]]],
                    ['q' => 'Solve: x² = 49.', 'options' => [['x = ±7', true], ['x = 7 only', false], ['x = 49', false]]],
                    ['q' => 'The mean of 4, 8, 10, 14 is…', 'options' => [['8', false], ['9', true], ['10', false]]],
                    ['q' => 'Area of a circle with r = 7 (π ≈ 22/7) is…', 'options' => [['154', true], ['44', false], ['49', false]]],
                    ['q' => 'log₁₀(1000) = ?', 'options' => [['2', false], ['3', true], ['10', false]]],
                    ['q' => 'sin(90°) = ?', 'options' => [['0', false], ['1', true], ['0.5', false]]],
                    ['q' => 'Probability of getting heads on a fair coin is…', 'options' => [['0', false], ['1/2', true], ['1', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: English',
                'description' => 'Advanced grammar, vocabulary and comprehension.',
                'subject_name' => 'English',
                'questions' => [
                    ['q' => 'Identify the figure of speech: "Time is a thief."', 'options' => [['Simile', false], ['Metaphor', true], ['Hyperbole', false]]],
                    ['q' => 'Choose the correct: She _____ to the market every morning.', 'options' => [['go', false], ['goes', true], ['going', false]]],
                    ['q' => 'A word opposite in meaning is an…', 'options' => [['Antonym', true], ['Synonym', false], ['Homonym', false]]],
                    ['q' => 'Which is a complex sentence?', 'options' => [['I ran.', false], ['Although it was raining, I ran.', true], ['I ran and walked.', false]]],
                    ['q' => 'Past participle of "write" is…', 'options' => [['Wrote', false], ['Written', true], ['Writed', false]]],
                    ['q' => 'A "soliloquy" is a…', 'options' => [['Group speech', false], ['Speech to oneself', true], ['Song', false]]],
                ],
            ],
            [
                'title' => 'Starter Quiz: Biology',
                'description' => 'Cells, systems and ecology.',
                'subject_name' => 'Biology',
                'questions' => [
                    ['q' => 'The powerhouse of the cell is the…', 'options' => [['Nucleus', false], ['Mitochondrion', true], ['Ribosome', false]]],
                    ['q' => 'DNA is found in the…', 'options' => [['Cytoplasm', false], ['Nucleus', true], ['Cell wall', false]]],
                    ['q' => 'The exchange of gases happens in the…', 'options' => [['Alveoli', true], ['Stomach', false], ['Liver', false]]],
                    ['q' => 'Which blood cells fight infection?', 'options' => [['Red blood cells', false], ['White blood cells', true], ['Platelets', false]]],
                    ['q' => 'Insulin is produced by the…', 'options' => [['Liver', false], ['Pancreas', true], ['Kidney', false]]],
                    ['q' => 'Photosynthesis requires…', 'options' => [['Light, water, CO₂', true], ['Heat only', false], ['Soil only', false]]],
                ],
            ],
        ];
    }
}
