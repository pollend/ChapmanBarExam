<?php


namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;


/**
 * Class Quiz
 * @package App
 * @ORM\Table(name="text_block_question")
 * @ORM\HasLifecycleCallbacks
 */
class TextBlockQuestion extends QuizQuestion
{
    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     */
    protected $content;
}