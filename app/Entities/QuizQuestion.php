<?php
namespace App;

use Doctrine\ORM\Mapping AS ORM;

abstract class QuizQuestion
{

    /**
     * @var integer
     * @ORM\Column(name="`order`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $order;

    /**
     * @var integer
     * @ORM\Column(name="`group`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $group;

    function getOrder(){
        return $this->order;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order){
        $this->order = $order;
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group){
        $this->group = $group;
        return $this;
    }

    public function getGroup(){
        return $this->group;
    }

    abstract function getTypeAttribute();

    abstract function answers();
}