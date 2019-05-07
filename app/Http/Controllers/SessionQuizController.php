<?php


namespace App\Http\Controllers;


use App\Entities\Quiz;
use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use App\Entities\ShortAnswerQuestion;
use App\Entities\ShortAnswerResponse;
use App\Entities\User;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizRepository;
use App\Repositories\QuizSessionRepository;
use App\Repositories\ShortAnswerQuestionRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Http\Request;
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

        $quiz = $repository->find($id);
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
        $sessionRepository = \EntityManager::getRepository(QuizSession::class);

        if($quiz = $quizRepository->find($id)){
            $user = Auth::user();
            if($sessionRepository->getActiveSession($user) != null)
                abort(404);
            if($quiz->isOpen($user) === false)
                abort(404);

            $session = new QuizSession();

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
        $sessionRepository = \EntityManager::getRepository(QuizSession::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);

        $user = Auth::user();
        /** @var QuizSession $session */
        if ($session = $sessionRepository->getActiveSession($user)) {
            if ($session->getId() == $session_id) {
                $quiz = $session->quiz();

                $groups = $questionRepository->getUniqueGroups($quiz);
                $questions = $questionRepository->filterByGroup($groups[$page], $quiz);

                return view('quiz.question', [
                    'questions' => $questions,
                    'page' => $page,
                    'session' => $session,
                    'maxPage' => Collection::make($groups)->keys()->max(),
                    'session_id' => $session_id
                ]);
            }
        }
        abort(404);
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
        /** @var User $user */
        $user = Auth::user();

        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = \EntityManager::getRepository(QuizSession::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);


        if($session = $sessionRepository->getActiveSession($user)){
            if ($session->getId() == $session_id) {
                /** @var Quiz $quiz */
                $quiz = $session->quiz;
                $groups = $questionRepository->getUniqueGroups($quiz);
                $group = $groups[$page];

                /** @var ShortAnswerQuestionRepository $shortAnswerRepository */
                $shortAnswerRepository = \EntityManager::getRepository(ShortAnswerQuestion::class);

                if ($request->has('short_answer')) {
                    foreach ($request->get('short_answer') as $key => $value) {
                        if(!empty($value)) {
                            /** @var ShortAnswerQuestion $question */
                            if($question = $shortAnswerRepository->findOneBy(['quiz' => $quiz, 'group' => $group, 'id' => $key])){
                               $response = $question->answersBySession($session)->first();
                               if($response === null){
                                   $response = new ShortAnswerResponse();
                                   $response->setQuestion($question);
                                   $response->setSession($session);
                               }
                                $response->setContent($value);

                                \EntityManager::persist($response);
                            }
                        }
                    }
                }
                if ($request->has('multiple_choice')) {

                }
            }
        }
        \EntityManager::flush();



//        if ($request->has('short_answer')) {
//            foreach ($request->get('short_answer') as $key => $value) {
//
//
//                /** @var ShortAnswerQuestionRepository $shortAnswerQuestionRepository */
//                $shortAnswerQuestionRepository = \EntityManager::getRepository(ShortAnswerQuestion::class);
//                /** @var ShortAnswerQuestion $question */
//                if($question = $shortAnswerQuestionRepository->find($key)){
//
//                }
//
//
//                if (!empty($value)) {
//
//                    /** @var ShortAnswerQuestion $question */
//                    $question = $quiz->shortAnswerQuestions()
//                        ->where('quiz_id', $quiz->id)
//                        ->where('group', $group_id)
//                        ->where('id', $key)
//                        ->first();
//                    if (isset($question)) {
//                        $this->submitShortAnswer($question, $session, $value);
//                    }
//                }
//
//            }
//        }
//
//        if ($request->has('multiple_choice')) {
//            foreach ($request->get('multiple_choice') as $key => $value) {
//                /** @var MultipleChoiceQuestion $question */
//                $question = $quiz->multipleChoiceQuestions()
//                    ->where('quiz_id', $quiz->id)
//                    ->where('group', $group_id)
//                    ->where('id', $key)
//                    ->first();
//
//                $entry = $question->entries()
//                    ->where('quiz_multiple_choice_question_id', $question->id)
//                    ->where('id', $value)
//                    ->first();
//
//                if (isset($question) && isset($entry)) {
//                    $this->submitMultipleChoiceAnswer($question, $session, $entry);
//                }
//            }
//        }
//
//        if ($request->has('action')) {
//            if ($request->get('action') === 'back') {
//                return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page - 1]);
//            }
//
//            if ($request->get('action') === 'next') {
//                // submit and close session
//                if ($group_id >= $groups->max()) {
//                    \Debugbar::info('closing Session');
//                    $session->submitted_at = Carbon::now();
//                    $session->save();
//                    return redirect()->route('home');
//                }
//                return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page + 1]);
//            }
//        }

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