<?php

namespace App\Entities\Traits;

use Doctrine\ORM\Mapping AS ORM;

trait TimestampTrait
{

    /**
     * @ORM\Column(name="created_at",type="datetime",nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at",type="datetime",nullable=false)
     */
    protected $updatedAt;

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt){
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt){
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

}