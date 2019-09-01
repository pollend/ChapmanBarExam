<?php


namespace App\Message;


use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Classroom;
use App\Entity\Quiz;

/**
 * @ApiResource()
 */
class StandardItemReport extends AbstractAsyncMessage
{

    private $quiz;
    private $classroom;

    public function __construct(Classroom $classroom,Quiz $quiz)
    {
        $this->classroom = $classroom;
        $this->quiz = $quiz;
    }

    /**
     * @ApiProperty(identifier=true)
     */
    public function getId(){
        return $this->quiz->getId();

    }

    /**
     * @return Quiz
     * @ApiProperty(readable=true)
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    /**
     * @return Classroom
     * @ApiProperty(readable=true)
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;
    }

    public function getKey(){
        return 'report_' . $this->quiz->getId() . '_' . $this->classroom->getId();
    }




}
