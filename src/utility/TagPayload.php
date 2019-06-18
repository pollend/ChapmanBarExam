<?php


namespace App\utility;


use App\Entity\QuestionTag;
use App\Event\QuestionResultsEvent;
use Symfony\Component\Serializer\Annotation\Groups;

class TagPayload{
    /**
     * @Groups({"tag_payload"})
     */
    private $result;

    /**
     * @Groups({"tag_payload"})
     */
    private $tag;

    public function __construct(QuestionResultsEvent $result, QuestionTag $tag)
    {
        $this->result = $result;
        $this->tag = $tag;
    }

    /**
     * @return QuestionResultsEvent
     */
    public function getResult(): QuestionResultsEvent
    {
        return $this->result;
    }

    /**
     * @return QuestionTag
     */
    public function getTag(): QuestionTag
    {
        return $this->tag;
    }
}