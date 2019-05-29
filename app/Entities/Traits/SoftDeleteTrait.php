<?php


namespace App\Entities\Traits;


trait SoftDeleteTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="deleted_at",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $deletedAt;
}