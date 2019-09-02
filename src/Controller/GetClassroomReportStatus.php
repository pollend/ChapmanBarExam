<?php


namespace App\Controller;


use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Message\DistributionReport;
use App\Message\StandardItemReport;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RequestContext;

class GetClassroomReportStatus
{

    private $cache;
    private $context;
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager,RequestContext $context, AdapterInterface $cache)
    {
        $this->cache = $cache;
        $this->context = $context;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Classroom $classroom, Request $request, $report_id,$type)
    {
        $this->context->setParameter('report_id', $report_id);
        $this->context->setParameter('type', $type);
        /** @var QuizRepository $quizSessionRepository */
        $quizRepository = $this->entityManager->getRepository(Quiz::class);

        /** @var Quiz $quiz */
        if ($quiz = $quizRepository->find($report_id)) {
            if($type == 'distribution') {

                $report = new DistributionReport($classroom->getId(), $quiz->getId());
                if ($this->cache->hasItem(DistributionReport::getStatusKey($report))) {
                    return $this->cache->getItem(DistributionReport::getStatusKey($report))->get();
                }
            }
            elseif ($type == 'item'){
                $report = new StandardItemReport($classroom->getId(), $quiz->getId());
                if ($this->cache->hasItem(StandardItemReport::getStatusKey($report))) {
                    return $this->cache->getItem(StandardItemReport::getStatusKey($report))->get();
                }
            }
            throw new NotFoundHttpException();
        }
    }
}
