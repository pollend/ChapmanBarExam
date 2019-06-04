<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;
/**
 * Class Quiz
 * @package App
 * @ORM\Entity()
 * @ORM\Table(name="short_answer_response")
 * @ORM\HasLifecycleCallbacks
 */
class ShortAnswerResponse extends QuizResponse
{

    /**
     * @var string
     * @ORM\Column(name="content", type="text",nullable=true)
     * @JMS\Groups({"correct"})
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }


    /**
     * @param string $content
     */
    public function setContent(string $content): ShortAnswerResponse
    {
        $this->content = $content;
        return $this;
    }



}