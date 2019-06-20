<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait TimestampTrait
 * @package App\Entity\Traits
 */
trait TimestampTrait
{
    /**
     * @ORM\Column(name="created_at",type="datetime",nullable=false)
     * @Groups({"timestamp"})
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at",type="datetime",nullable=false)
     * @Groups({"timestamp"})
     */
    protected $updatedAt;

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
