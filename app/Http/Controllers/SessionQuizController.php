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
        $user = Auth::user();
        $quiz = Quiz::query()->where('id',$id)->firstOrFail();
        if(!$this->quizRepository->isOpen($quiz,$user)) {
            abort(404);
        }

        return view('quiz.start', ['quiz_id' => $id]);
    }

    public function start($id)
    {
        $quiz = Quiz::query()->where('id', $id)->firstOrFail();
        $user = Auth::user();
        $session = $this->sessionRepository->startSession($user, $quiz);
        return redirect()->route('quiz.question', ['session_id' => $session->id, 'page' => 0]);
    }


    public function questionForm($session_id, $page)
    {
        $user = Auth::user();
        $session = $this->sessionRepository->getActiveSession($user);
        if (!isset($session) || $session->id != $session_id) {
            abort(404);
        }
        /** @var Quiz $quiz */
        $quiz = $session->quiz;
        $questions = $this->quizRepository->getGroupedQuestions($quiz);
        $maxPage = $questions->keys()->max();

        return view('quiz.question', [
            'questions' => $questions[$page],
            'page' => $page,
            'session' => $session,
            'maxPage' => $maxPage,
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
        $groups = $this->quizRepository->getGroups($quiz);
        $group_id = $groups[$page];

        if ($request->has('short_answer')) {
            foreach ($request->get('short_answer') as $key => $value) {

                if (!empty($value)) {

                    /** @var QuizShortAnswerQuestion $question */
                    $question = $quiz->shortAnswerQuestions()
                        ->where('quiz_id', $quiz->id)
                        ->where('group', $group_id)
                        ->where('id', $key)
                        ->first();
                    if (isset($question)) {
                        $this->submitShortAnswer($question, $session, $value);
                    }
                }

            }
        }

        if ($request->has('multiple_choice')) {
            foreach ($request->get('multiple_choice') as $key => $value) {
                /** @var QuizMultipleChoiceQuestion $question */
                $question = $quiz->multipleChoiceQuestions()
                    ->where('quiz_id', $quiz->id)
                    ->where('group', $group_id)
                    ->where('id', $key)
                    ->first();

                $entry = $question->entries()
                    ->where('quiz_multiple_choice_question_id', $question->id)
                    ->where('id', $value)
                    ->first();

                if (isset($question) && isset($entry)) {
                    $this->submitMultipleChoiceAnswer($question, $session, $entry);
                }
            }
        }

        if ($request->has('action')) {
            if ($request->get('action') === 'back') {
                return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page - 1]);
            }

            if ($request->get('action') === 'next') {
                // submit and close session
                if ($group_id >= $groups->max()) {
                    \Debugbar::info('closing Session');
                    $session->is_open = false;
                    $session->save();
                    return redirect()->route('home');
                }
                return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page + 1]);
            }
        }

        return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page]);
    }

    private function submitShortAnswer($question,$session,$content){
        $answer = $question->answers()
            ->bySession($session)
            ->firstOrNew([]);
        $answer->quiz_session_id = $session->id;
        $answer->quiz_short_answer_question_id = $question->id;
        $answer->content = $content;
        $answer->save();
    }

    private function submitMultipleChoiceAnswer($question,$session,$entry){
        $answer = $question->answers()
            ->bySession($session)
            ->firstOrNew([]);
        $answer->quiz_session_id = $session->id;
        $answer->quiz_multiple_choice_question_id = $question->id;
        $answer->quiz_multiple_choice_entry_id = $entry->id;
        $answer->save();
    }

}