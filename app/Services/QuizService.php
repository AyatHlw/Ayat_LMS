<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\StudentAnswer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function createQuiz(Request $request)
    {
        DB::beginTransaction();

        try {
            $quiz = Quiz::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'course_id' => $request->input('course_id'),
            ]);

            foreach ($request->input('questions') as $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    $question->answers()->create([
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }

            DB::commit();

            return [
                'message' => 'Quiz created successfully',
                'quiz' => $quiz
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function showQuizForTeachers($quizId)
    {
        try {
            $quiz = Quiz::with(['questions.answers'])->findOrFail($quizId);
            return $quiz;
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Quiz not found.');
        }
    }

    public function showQuizForStudents($quizId)
    {
        $quiz = Quiz::with(['questions.answers'])
            ->where('id', $quizId)
            ->first();

        if (!$quiz) {
            throw new \Exception('Quiz not found.');
        }
        $quiz->questions->each(function ($question) {
            $question->answers->each(function ($answer) {
                unset($answer->is_correct);
            });
        });

        return $quiz;
    }

    public function checkAnswers(Request $request)
    {
        DB::beginTransaction();

        try {
            $quizId = $request->input('quiz_id');
            $answers = $request->input('answers'); // array of question_id => answer_id

            $correctAnswersCount = 0;
            $totalQuestions = count($answers);

            foreach ($answers as $questionId => $answerId) {
                $isCorrect = Answer::where('id', $answerId)->value('is_correct');
                if ($isCorrect) {
                    $correctAnswersCount++;
                }

                StudentAnswer::create([
                    'student_id' => Auth::id(),
                    'quiz_id' => $quizId,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                ]);
            }

            $passed = $correctAnswersCount >= ($totalQuestions / 2);

            QuizResult::create([
                'student_id' => Auth::id(),
                'quiz_id' => $quizId,
                'passed' => $passed
            ]);

            DB::commit();

            return [
                'message' => $passed ? 'Student passed the quiz' : 'Student failed the quiz',
                'correct_answers' => $correctAnswersCount,
                'total_questions' => $totalQuestions,
                'passed' => $passed
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateQuiz($quizId, $data)
    {

        DB::beginTransaction();

        try {
            $quiz = Quiz::findOrFail($quizId);
            $quiz->update($data);

            if (isset($data['questions'])) {
                foreach ($data['questions'] as $questionData) {
                    $question = Question::updateOrCreate(
                        ['id' => $questionData['id'] ?? null, 'quiz_id' => $quizId],
                        ['question_text' => $questionData['question_text']]
                    );

                    if (isset($questionData['answers'])) {
                        foreach ($questionData['answers'] as $answerData) {
                            Answer::updateOrCreate(
                                ['id' => $answerData['id'] ?? null, 'question_id' => $question->id],
                                [
                                    'answer_text' => $answerData['answer_text'],
                                    'is_correct' => $answerData['is_correct'] ?? false
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();

            return $quiz;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteQuestion($questionId)
    {
        DB::beginTransaction();

        try {
            $question = Question::findOrFail($questionId);

            $question->answers()->delete(); //delete all answers for this question
            $question->delete();            //delete the question

            DB::commit();

            return ['message' => 'Question and its answers deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
