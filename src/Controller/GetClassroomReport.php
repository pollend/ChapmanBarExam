<?php
namespace App\Controller;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use App\Entity\Classroom;
use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\JobRunning;
use App\Message\DistributionReport;
use App\Message\StandardItemReport;
use App\Repository\MultipleChoiceResponseRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Cache\CacheInterface;

class GetClassroomReport
{
    private $entityManager;
    protected $context;
    protected $bus;
    private $cache;
    private $resourceClassResolver;

    public function __construct(ResourceClassResolverInterface $resourceClassResolver, AdapterInterface $cache, EntityManagerInterface $entityManager, RequestContext $context, MessageBusInterface $bus)
    {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->context = $context;
        $this->cache = $cache;

        $this->resourceClassResolver = $resourceClassResolver;
    }

    public function __invoke(Classroom $classroom, Request $request, $report_id,$type)
    {
        /** @var QuizRepository $quizSessionRepository */
        $quizRepository = $this->entityManager->getRepository(Quiz::class);

        $this->context->setParameter('report_id', $report_id);
        $this->context->setParameter('type', $type);

        /** @var Quiz $quiz */
        if ($quiz = $quizRepository->find($report_id)) {
            if($type == 'distribution') {
                $report = new DistributionReport($classroom->getId(), $quiz->getId());
                if ($this->cache->hasItem(DistributionReport::getKey($report))) {
                    return $this->cache->getItem(DistributionReport::getKey($report))->get();
                } else {
                    $this->bus->dispatch($report);
                }
                return $report;
            }
            elseif ($type == 'item'){
                $report = new StandardItemReport($classroom->getId(), $quiz->getId());
                if ($this->cache->hasItem(StandardItemReport::getKey($report))) {
                    return $this->cache->getItem(StandardItemReport::getKey($report))->get();
                } else {
                    $this->bus->dispatch($report);
                }
                return $report;
            }
        }
        return [];
    }
}
