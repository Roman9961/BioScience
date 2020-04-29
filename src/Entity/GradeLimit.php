<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class GradeLimit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $gradeLimit;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->gradeLimit;
    }

    /**
     * @param mixed $gradeLimit
     */
    public function setLimit($gradeLimit)
    {
        $this->gradeLimit = $gradeLimit;
    }


}
