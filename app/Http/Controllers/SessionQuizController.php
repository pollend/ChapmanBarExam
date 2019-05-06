<?php


namespace App\Http\Controllers;


use App\Exceptions\QuizClosedException;
use App\Exceptions\SessionInProgressException;
use App\Quiz;
use App\MultipleChoiceQuestion;
use App\QuizQuestion;
use App\QuizSession;
use App\Repositories\QuizRepository;
use App\Repositories\QuizSessionRepository;
use App\ShortAnswerQuestion;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepositoryInterface;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Controller
{


    public function __construct()
    {
    }

    public function startForm($id)
    {
        $user = Auth::user();
        /** @var QuizRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\Quiz::class);

        $quiz = $repository->byId($id);
        if($quiz  == null)
            abort(404);
        if(!$quiz->isOpen($user))
            abort(404);

        return view('quiz.start', ['quiz_id' => $id]);
    }

    public function start($id)
    {
        /** @var QuizRepository $quizRepository */
        $quizRepository = \EntityManager::getRepository(\App\Entities\Quiz::class);
        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = \EntityManager::getRepository(\App\Entities\QuizSession::class);

        if($quiz = $quizRepository->byId($id)){
            $user = Auth::user();
            if($sessionRepository->getActiveSession($user) != null)
                abort(404);
            if($quiz->isOpen($user) === false)
                abort(404);

            $session = new \App\Entities\QuizSession();

            $session->setOwner($user);
            $session->setQuiz($quiz);
            \EntityManager::persist($session);
            \EntityManager::flush();
            return redirect()->route('quiz.question', ['session_id' => $session->getId(), 'page' => 0]);
        }
        abort(404);
    }


    public function questionForm($session_id, $page)
    {

        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = \EntityManager::getRepository(\App\Entities\QuizSession::class);

        $user = Auth::user();
        /** @var \App\Entities\QuizSession $session */
        if($session = $sessionRepository->getActiveSession($user)){
            \Debugbar::info('Found Active Session');
            if( $session->getId() === $session_id){
                \Debugbar::info('Session Matches');

                $quiz = $session->quiz();

                $colection = \Illuminate\Support\Collection::make()
                    ->merge($quiz->getMultipleChoiceQuestions()->toArray())
                    ->merge($quiz->getShortAnswerQuestions()->toArray());

                $questions = $colection->sortBy('group')
                    ->groupBy('group')
                    ->transform(function ($entry) {
                        return $entry->sortby('order')->values();
                    })->values();

                return view('quiz.question', [
                    'questions' => $questions[$page],
                    'page' => $page,
                    'session' => $session,
                    'maxPage' => $questions->keys()->max(),
                    'session_id' => $session_id
                ]);
            }
        }
        abort(404);

//
//        $session = $this->sessionRepository->getActiveSession($user);
//        if (!isset($session) || $session->id != $session_id) {
//            abort(404);
//        }
//        /** @var Quiz $quiz */
//        $quiz = $session->quiz;
//        $questions = $this->quizRepository->getGroupedQuestions($quiz);
//        $maxPage = $questions->keys()->max();
//
//
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

                    /** @var ShortAnswerQuestion $question */
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
                /** @var MultipleChoiceQuestion $question */
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
                    $session->submitted_at = Carbon::now();
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