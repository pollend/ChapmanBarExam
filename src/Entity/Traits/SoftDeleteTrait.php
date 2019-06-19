<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;

/**
 * Trait SoftDeleteTrait
 * @package App\Entity\Traits
 */
trait SoftDeleteTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="deleted_at",type="datetime",nullable=true)
     * @Groups({"list"})
     */
    protected $deletedAt;

    /**
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }
}
