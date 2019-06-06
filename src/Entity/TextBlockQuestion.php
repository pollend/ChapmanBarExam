<?php


namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity()
 * @ORM\Table(name="text_block_question")
 * @ORM\HasLifecycleCallbacks
 */
class TextBlockQuestion extends QuizQuestion
{
    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    protected $content;



    /**
     * @param string $content
     * @return TextBlockQuestion
     */
    public function setContent(string $content): TextBlockQuestion
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}