<?php


namespace App\Http\Controllers;


use App\Entities\MultipleChoiceEntry;
use App\Entities\MultipleChoiceQuestion;
use App\Entities\MultipleChoiceResponse;
use App\Entities\Quiz;
use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use App\Entities\ShortAnswerQuestion;
use App\Entities\ShortAnswerResponse;
use App\Entities\User;
use App\Repositories\MultipleChoiceEntryRepository;
use App\Repositories\MultipleChoiceQuestionRepository;
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
                $quiz = $session->getQuiz();

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
        /** @var User $user */
        $user = Auth::user();

        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = \EntityManager::getRepository(QuizSession::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);


        /** @var QuizSession $session */
        if($session = $sessionRepository->getActiveSession($user)){
            if ($session->getId() == $session_id) {
                /** @var Quiz $quiz */
                $quiz = $session->getQuiz();
                $groups = $questionRepository->getUniqueGroups($quiz);
                $group = $groups[$page];


                if ($request->has('short_answer')) {
                    /** @var ShortAnswerQuestionRepository $shortAnswerRepository */
                    $shortAnswerRepository = \EntityManager::getRepository(ShortAnswerQuestion::class);

                    foreach ($request->get('short_answer') as $key => $value) {
                        if (!empty($value)) {
                            /** @var ShortAnswerQuestion $question */
                            if ($question = $shortAnswerRepository->findOneBy(['quiz' => $quiz, 'group' => $group, 'id' => $key])) {
                                $response = $question->answersBySession($session)->first();
                                if ($response === false) {
                                    $response = new ShortAnswerResponse();
                                    $response->setQuestion($question);
                                    $response->setSession($session);
                                }
                                if ($response instanceof ShortAnswerResponse) {
                                    $response->setContent($value);
                                    \EntityManager::persist($response);
                                }
                            }
                        }
                    }
                }
                if ($request->has('multiple_choice')) {
                    /** @var MultipleChoiceQuestionRepository $multipleChoiceRepository */
                    $multipleChoiceRepository = \EntityManager::getRepository(MultipleChoiceQuestion::class);
                    /** @var MultipleChoiceEntryRepository $multipleChoiceEntryRepository */
                    $multipleChoiceEntryRepository = \EntityManager::getRepository(MultipleChoiceEntry::class);

                    foreach ($request->get('multiple_choice') as $key => $value) {
                        if($question = $multipleChoiceRepository->findOneBy(['quiz' => $quiz, 'group' => $group, 'id' => $key])){
                            $response = $question->answersBySession($session)->first();
                            if($response === false){
                                $response = new MultipleChoiceResponse();
                                $response->setQuestion($question);
                                $response->setSession($session);
                            }
                            if($response instanceof MultipleChoiceResponse){
                                $entries = $multipleChoiceEntryRepository->getEntriesForQuestion($question);
                                /** @var MultipleChoiceEntry $entry */
                                foreach ($entries as $entry){
                                    if($entry->getId() == $value){
                                        $response->setChoice($entry);
                                    }
                                }
                                \EntityManager::persist($response);
                            }
                        }
                    }
                }
            }
            \EntityManager::flush();

            $groups = $questionRepository->getUniqueGroups($quiz);
            if ($request->has('action')) {
                if ($request->get('action') === 'back') {
                    return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page - 1]);
                }
                if ($request->get('action') === 'next') {
                    // submit and close session
                    if ($page >= Collection::make($groups)->keys()->max()) {
                        $session->setSubmittedAt(Carbon::now());
                        \EntityManager::persist($session);
                        \EntityManager::flush();
                        return redirect()->route('home');
                    }
                    return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page + 1]);
                }
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