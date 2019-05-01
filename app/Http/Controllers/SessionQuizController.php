<?php


namespace App\Http\Controllers;


use App\Exceptions\SessionInProgressException;
use App\Quiz;
use App\QuizMultipleChoiceQuestion;
use App\QuizSession;
use App\QuizShortAnswerQuestion;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepositoryInterface;
use DebugBar\DebugBar;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Controller
{

    private $sessionRepository;
    private $quizRepository;

    public function __construct(SessionRepositoryInterface $sessionRepository, QuizRepositoryInterface $quizRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->quizRepository = $quizRepository;
    }

    public function startForm($id)
    {
        return view('quiz.start', ['quiz_id' => $id]);
    }

    public function start($id)
    {
        $quiz = Quiz::query()->where('id', $id)->firstOrFail();
        $user = Auth::user();
        $session = null;
        $session = $this->sessionRepository->startSession($user, $quiz);
        return redirect()->route('quiz.question', ['session_id' => $session->id, 'page' => 0]);
    }

    public function questionForm($session_id, $page)
    {
        $user = Auth::user();
        $session = $this->sessionRepository->getActiveSession($user);
        if ($session->id != $session_id) {
            abort(404);
        }
        /** @var Quiz $quiz */
        $quiz = $session->quiz;
        $questions = $this->quizRepository->getQuestions($quiz);

        \Debugbar::info($questions);
        return view('quiz.question', [
            'questions' => $questions[$page],
            'page' => $page,
            'session' => $session,
            'maxPage' => $questions->keys()->max(),
            'session_id' => $session_id
        ]);

    }

    /**
     * @param $request
     * @param $session_id
     * @param $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function question(Request $request, $session_id, $page)
    {

        \Debugbar::info($request->all());

        $user = Auth::user();
        $session = $this->sessionRepository->getActiveSession($user);
        /** @var Quiz $quiz */
        $quiz = $session->quiz;
        $group_id = $this->quizRepository->getGroups($quiz)[$page];

        if ($request->has('short_answer')) {
            foreach ($request->get('short_answer') as $key => $value) {

                if (!empty($value)) {

                    /** @var QuizShortAnswerQuestion $question */
                    $question = $quiz->shortAnswerQuestions()
                        ->where('quiz_id', $quiz->id)
                        ->where('group', $group_id)
                        ->where('id', $key)
                        ->first();
                    if ($question) {
                        $answer = $question->answers()
                            ->bySession($session)
                            ->firstOrNew([]);
                        $answer->quiz_session_id = $session->id;
                        $answer->quiz_short_answer_question_id = $question->id;
                        $answer->content = $value;
                    }
                    $answer->save();
                }

            }
        }

        if ($request->has('multiple_choice')) {
            foreach ($request->get('multiple_choice') as $key => $value) {
                $entry_id = array_keys($value->keys())[0];

                /** @var QuizMultipleChoiceQuestion $question */
                $question = $quiz->multipleChoiceQuestions()
                    ->where('quiz_id', $quiz->id)
                    ->where('group', $group_id)
                    ->where('id', $key)
                    ->first();

                $entry = $question->entries()
                    ->where('quiz_multiple_choice_question_id', $question->id)
                    ->where('id', $entry_id)
                    ->first();

                if ($question && $entry) {
                    $answer = $question->answers()
                        ->bySession($session)
                        ->firstOrNew([]);
                    $answer->quiz_session_id = $session->id;
                    $answer->quiz_short_answer_question_id = $question->id;
                    $answer->quiz_multiple_choice_entry_id = $entry->id;
                    $answer->save();
                    
                }
            }

        }

        return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page + 1]);
    }

    public function endForm()
    {

    }

}