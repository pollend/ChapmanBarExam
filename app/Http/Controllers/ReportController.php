<?php


namespace App\Http\Controllers;

use App\Entities\MultipleChoiceQuestion;
use App\Entities\QuestionTag;
use App\Entities\QuizQuestion;
use App\Entities\QuizResponse;
use App\Entities\QuizSession;
use App\Repositories\QuestionRepository;
use App\Repositories\QuestionTagRepository;
use App\Repositories\QuizResponseRepository;
use App\Repositories\QuizSessionRepository;
use App\Utility\QuestionResult;
use Doctrine\Common\Collections\Criteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class ReportController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        return view('report.index');
    }

    public function show(Request $request, $report)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = \EntityManager::getRepository(QuizSession::class);

        /** @var QuizResponseRepository $quizResponseRepository */
        $quizResponseRepository = \EntityManager::getRepository(QuizResponse::class);

        $user = \Auth::user();
        /** @var QuizSession $session */
        if ($session = $quizSessionRepository->findOneBy(['id' => $report, 'owner' => $user])) {

            $responses = Collection::make($quizResponseRepository->filterResponsesBySession($session))->keyBy(function ($response) {
                return $response->getQuestion()->getId();
            });
            $questions = Collection::make($session->getQuiz()->getQuestions()->matching(Criteria::create()->orderBy(['group' =>'ASC' ,'order' => 'ASC'])));

            return view('report.show', ['questions' => $questions,'responses' => $responses,'session' => $session]);
        }
        abort(404);
        return null;
    }

    public function breakdown(Request $request, $report)
    {

        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = \EntityManager::getRepository(QuizSession::class);

        /** @var QuizResponseRepository $quizResponseRepository */
        $quizResponseRepository = \EntityManager::getRepository(QuizResponse::class);

        /** @var QuestionTagRepository $questionTagRepository */
        $questionTagRepository =  \EntityManager::getRepository(QuestionTag::class);

        /** @var QuestionRepository $questionRepository */
        $questionRepository =  \EntityManager::getRepository(QuizQuestion::class);


        $user = \Auth::user();
        /** @var QuizSession $session */
        if ($session = $quizSessionRepository->findOneBy(['id' => $report, 'owner' => $user])) {

            $tags =  Collection::make($questionTagRepository->getUniqueTagsForQuiz($session->getQuiz()))->keyBy(function ($tag) {
                return $tag->getId();
            });
            $responses = Collection::make($quizResponseRepository->filterResponsesBySession($session))->keyBy(function ($response) {
                return $response->getQuestion()->getId();
            });

            $breakdown = Collection::make();
            /** @var QuestionTag $tag */
            foreach ($tags as $tag){
                $questionsByTag = $questionRepository->filterByTag($session->getQuiz(),$tag);
                $breakdown->put($tag->getId(),new QuestionResult($questionsByTag,$session));
            }

            \Debugbar::debug($breakdown);
            return view('report.breakdown',['session' => $session, 'tags' => $tags,'breakdown' => $breakdown]);
        }
        abort(404);
        return null;
    }
}