<?php


namespace App\Message;


class DistributionReport extends AbstractAsyncMessage
{
    private $quizId;
    private $classroomId;

    public function __construct(int $classroom, int $quiz)
    {
        $this->classroomId = $classroom;
        $this->quizId = $quiz;
    }

    public function getQuizId(): int
    {
        return $this->quizId;
    }

    public function getClassroomId(): int
    {
        return $this->classroomId;
    }

    public static function getKey(DistributionReport $report)
    {
        return 'report_' . $report->quizId . '_' . $report->classroomId . '_distribution';
    }

    public static function getStatusKey(DistributionReport $report)
    {
        return 'report_' . $report->quizId . '_' . $report->classroomId . '_distribution_status';
    }
}
