<?php


namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity(repositoryClass="App\Repositories\MultipleChoiceEntryRepository")
 * @ORM\Table(name="multiple_choice")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MultipleChoiceEntry
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="`order`",type="smallint",nullable=false)
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     */
    protected $content;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="MultipleChoiceQuestion",inversedBy="entries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="multiple_choice_question_id", referencedColumnName="id")
     * })
     */
    protected $question;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getId(){
        return $this->id;
    }

}