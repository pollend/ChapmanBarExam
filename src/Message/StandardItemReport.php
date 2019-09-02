<?php


namespace App\Message;


use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Classroom;
use App\Entity\Quiz;

class StandardItemReport extends AbstractAsyncMessage
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

    public static function getKey(StandardItemReport $report)
    {
        return 'report_' . $report->quizId . '_' . $report->classroomId . '_item';
    }

    public static function getStatusKey(StandardItemReport $report)
    {
        return 'report_' . $report->quizId . '_' . $report->classroomId . '_item_status';
    }
}
