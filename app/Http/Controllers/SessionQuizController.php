<?php


namespace App\Http\Controllers;


use App\Console\Commands\RecalculateScores;
use App\Entities\Classroom;
use App\Entities\MultipleChoiceEntry;
use App\Entities\MultipleChoiceQuestion;
use App\Entities\MultipleChoiceResponse;
use App\Entities\Quiz;
use App\Entities\QuizAccess;
use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use App\Entities\ShortAnswerQuestion;
use App\Entities\ShortAnswerResponse;
use App\Entities\User;
use App\Jobs\ProcessQuizSession;
use App\Repositories\ClassroomRepository;
use App\Repositories\MultipleChoiceEntryRepository;
use App\Repositories\MultipleChoiceQuestionRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizAccessRepository;
use App\Repositories\QuizRepository;
use App\Repositories\QuizSessionRepository;
use App\Repositories\ShortAnswerQuestionRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Controller
{

    /** @var $classRepository ClassroomRepository  */
    private $classRepository ;
    /** @var $classRepository QuizAccessRepository */
    private $quizAccessRepository;
    /** @var $quizRepository QuizRepository */
    private $quizRepository;

    /**
     * SessionQuizController constructor.
     */
    public function __construct()
    {
        $this->classRepository =  \EntityManager::getRepository(Classroom::class);
        $this->quizAccessRepository = \EntityManager::getRepository(QuizAccess::class);
        $this->quizRepository = \EntityManager::getRepository(Quiz::class);
    }


    public function startForm($class_id,$quiz_id)
    {
        $user = Auth::user();

        /** @var Classroom $classroom */
        if($classroom = $this->classRepository->find($class_id)) {
            /** @var Quiz $quiz */
            if($quiz = $this->quizRepository->find($quiz_id)){
                try {
                    /** @var QuizAccess $access */
                    if ($access = $this->quizAccessRepository->getAccessByClass($classroom, $quiz)) {
                        if($access->isOpen($user)){
                            return view('quiz.start', ['quiz' => $quiz, 'class' => $classroom]);
                        }
                    }
                } catch (NoResultException $e) {
                    \Debugbar::error("unknown access for class and quiz");
                } catch (NonUniqueResultException $e) {
                    \Debugbar::error("unknown access for class and quiz");
                }

            }
        }
        abort(404);
        return null;
    }

    public function start($class_id,$quiz_id)
    {
        $user = Auth::user();

        /** @var Classroom $classroom */
        if($classroom = $this->classRepository->find($class_id)) {
            /** @var Quiz $quiz */
            if($quiz = $this->quizRepository->find($quiz_id)){
                try {
                    /** @var QuizAccess $access */
                    if ($access = $this->quizAccessRepository->getAccessByClass($classroom, $quiz)) {
                        if($access->isOpen($user)){
                          $session = new QuizSession();
                          $session->setOwner($user);
                          $session->setQuiz($quiz);
                          $session->setClassroom($classroom);
                          \EntityManager::persist($session);
                          \EntityManager::flush();
                           return redirect()->route('quiz.question', ['session_id' => $session->getId(), 'page' => 0]);
                        }
                    }
                } catch (NoResultException $e) {
                    \Debugbar::error("unknown access for class and quiz");
                } catch (NonUniqueResultException $e) {
                    \Debugbar::error("unknown access for class and quiz");
                }

            }
        }
        abort(404);
        return null;

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
        return null;
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
        if ($session = $sessionRepository->getActiveSession($user)) {
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
                        if ($question = $multipleChoiceRepository->findOneBy(['quiz' => $quiz, 'group' => $group, 'id' => $key])) {
                            $response = $question->answersBySession($session)->first();
                            if ($response === false) {
                                $response = new MultipleChoiceResponse();
                                $response->setQuestion($question);
                                $response->setSession($session);
                            }
                            if ($response instanceof MultipleChoiceResponse) {
                                $entries = $multipleChoiceEntryRepository->getEntriesForQuestion($question);
                                /** @var MultipleChoiceEntry $entry */
                                foreach ($entries as $entry) {
                                    if ($entry->getId() == $value) {
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
                        // process quiz and calculate score
                        ProcessQuizSession::dispatch($session);
                        return redirect()->route('home');
                    }
                    return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page + 1]);
                }
            }

        }

        return redirect()->route('quiz.question', ['session_id' => $session_id, 'page' => $page]);
    }

}